<?php

namespace App\Command;


use App\Entity\Audio;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'Limit audio', 0)
            ->addOption('offset', null, InputOption::VALUE_OPTIONAL, 'Offset audio list', 0)
            ->addOption('uid', null, InputOption::VALUE_OPTIONAL, 'VK user id', false)
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
        $uid = $input->getOption('uid');

        if (!$input->getOption('uid')) {
            $uid = $this->getParam('uid');
        }

        $output->writeln($this->getAudio($output, $input->getOption('limit'), $input->getOption('offset'), $uid));
    }

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @param int|null $uid
     * @return array
     */
    protected function getVaud(?int $limit = null, ?int $offset = null, ?int $uid): array
    {
        $alAudio = new Vaud\AlAudio($uid, $this->getCookiesAsArray());
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
     * @param int|null $uid
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getAudio(OutputInterface $output, $limit = 0, $offset = 0, ?int $uid)
    {
        $this->checkAuth();

        [$alAudio, $decoder] = $this->getVaud($limit, $offset, $uid);

        $countItems = count($alAudio->main());
        $progressBar = new ProgressBar($output, $countItems);

        foreach ($alAudio->main() as $key => $value) {
            if ($this->saveAudio($value, $uid)) {
                $this->downloadAudio($value['id'], $decoder->decode($value['url']), $uid);

                $clearedTrackName = trim($value['track']);
                $clearedArtistName = trim($value['artist']);

                $this->logger->info("Download audio: {$clearedArtistName} - {$clearedTrackName}.mp3");

                $progressBar->advance();
            }
        }

        $progressBar->finish();
    }

    /**
     * @param $track
     * @param $uid
     * @return bool
     */
    protected function saveAudio($track, $uid): bool
    {
        $entityManager = $this->getContainer()->get('doctrine')->getManager();

        $audio = $entityManager->getRepository(Audio::class)->findBy(['track_id' => $track['id']]);

        if (!$audio) {
            $audio = new Audio();
            $audio->setDownloaded(1);
            $audio->setArtistName($track['artist']);
            $audio->setTrackName($track['track']);
            $audio->setTrackId($track['id']);
            $audio->setUid($uid);
            $audio->setFilePath("/download/mp3/{$uid}/{$track['id']}.mp3");

            $entityManager->persist($audio);
            $entityManager->flush();

            $this->logger->info('Audio entity not fount. Saving new entity.');

            return true;
        }

        $this->logger->info('Audio entity exist. Skip.', $audio);

        return false;
    }

    /**
     * @param $track_id
     * @param $uid
     * @return string
     */
    protected function getTrackPath($track_id, $uid)
    {
        $downloadDir = $this->getParam('download_path') . '/mp3/' . $uid;

        is_dir($downloadDir) || mkdir($downloadDir, 0777, true);

        return $downloadDir . '/' . $track_id . '.mp3';
    }

    /**
     * @param $track_id
     * @param $track_url
     * @param $uid
     * @return bool
     */
    protected function downloadAudio($track_id, $track_url, $uid)
    {
        $fileName = $this->getTrackPath($track_id, $uid);
        $allowDownload = $this->overloadExistsTracks || !file_exists($fileName);

        if ($allowDownload) {
            return !!file_put_contents($fileName, @file_get_contents($track_url));
        }

        return true;
    }
}
