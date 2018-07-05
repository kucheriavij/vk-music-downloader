<?php

namespace App\Command;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use YuruYuri\Vaud;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;

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
            ->addArgument('limit', InputArgument::OPTIONAL, 'Limit audio', 0)
            ->addArgument('offset', InputArgument::OPTIONAL, 'Offset audio list', 0)
            ->setDescription('Download music');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln($this->getAudio($output, $input->getArgument('limit'), $input->getArgument('offset')));
    }

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    protected function getVaud(?int $limit = null, ?int $offset = null): array
    {
        $alAudio = new Vaud\AlAudio($this->getParam('uid'), $this->getCookiesAsArray());
        $decoder = new Vaud\Decoder($this->getParam('uid'));

        $limit = $limit ?? 0;
        $offset = $offset ?? 0;

        $alAudio->setLimitOffset($limit, $offset);

        return [$alAudio, $decoder];
    }

    /**
     * @param OutputInterface $output
     * @param int $limit
     * @param int $offset
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getAudio(OutputInterface $output, $limit = 0, $offset = 0)
    {
        $this->checkAuth();

        [$alAudio, $decoder] = $this->getVaud($limit, $offset);

        $countItems = count($alAudio->main());
        $progressBar = new ProgressBar($output, $countItems);

        foreach ($alAudio->main() as $key => $value) {
            $result = $this->downloadAudio($value['id'], $decoder->decode($value['url']));
            $progressBar->advance();
        }

        $progressBar->finish();
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
