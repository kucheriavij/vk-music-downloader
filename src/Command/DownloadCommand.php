<?php

namespace App\Command;

use App\Service\Params;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DownloadCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('vk:download')
            ->setDescription('Download music');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**
         * @var $params Params
         */
        $login = $this->getParam('login');

        $output->writeln($login);
    }
}
