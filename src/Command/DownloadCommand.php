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
        $q = $this->getVaud(5, 0);
//        $output->writeln($q);
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
}
