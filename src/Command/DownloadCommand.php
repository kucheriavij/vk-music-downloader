<?php

namespace App\Command;


use App\Service\Params;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use YuruYuri\Vaud;

/**
 * Class DownloadCommand
 *
 * @package App\Command
 */
class DownloadCommand extends AbstractCommand
{
    /**
     *
     */
    protected function configure()
    {
        $this->setName('vk:download')
            ->setDescription('Download music');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
//        $q = $this->getVaud(5, 0);
//        $output->writeln($q);
        print_r($this->getAudio());
    }

    protected function getVaud(?int $limit = null, ?int $offset = null): array
    {
        $alAudio = new Vaud\AlAudio($this->getParam('uid'), $this->getCookiesAsArray());
        $decoder = new Vaud\Decoder($this->getParam('uid'));

        $limit = $limit ?? 0;
        $offset = $offset ?? 0;

        $alAudio->setLimitOffset($limit, $offset);

        return [$alAudio, $decoder];
    }

    protected function getAudio()
    {
        $this->checkAuth();

        [$alAudio, $decoder] = $this->getVaud(5, 0);

        foreach ($alAudio->main() as $key => $value) {
            $result = $this->downloadAudio($value['id'], $decoder->decode($value['url']));

            print_r($result);
        }
    }

    /**
     * @param $track_id
     * @return string
     */
    protected function getTrackPath($track_id)
    {
        $downloadDir = $this->getParam('download_path') . '/mp3';

        is_dir($downloadDir) || mkdir($downloadDir, 0777, true);

        return $downloadDir . '/' . $track_id . '.mp3';
    }

    /**
     * @param $track_id
     * @param $track_url
     * @return bool
     */
    protected function downloadAudio($track_id, $track_url)
    {
        $fileName = $this->getTrackPath($track_id);
        $allowDownload = $this->overloadExistsTracks || !file_exists($fileName);

        if ($allowDownload) {
            return !!file_put_contents($fileName, @file_get_contents($track_url));
        }

        return true;
    }
}
