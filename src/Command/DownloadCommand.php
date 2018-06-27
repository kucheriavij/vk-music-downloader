<?php

namespace App\Command;

use App\Traits\Http;
use Bavix\AdvancedHtmlDom;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DownloadCommand extends Command
{
    use Http;

    protected $overloadExistsTracks = false;

    /**
     * @var array
     */
    protected $defaultHeaders = [
        'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
        'content-type' => 'application/x-www-form-urlencoded',
        'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.86 Safari/537.36',
        'referer' => 'https://vk.com'
    ];

    /**
     * @var array
     */
    protected $requestOptions = [
        'base_uri' => 'https://vk.com',
    ];

    /**
     * @var int
     */
    protected $uid = 0;

    /**
     * @var int
     */
    public $limit = 100;

    /**
     * @var int
     */
    public $offset = 0;

    /**
     * @param string $actionID
     * @return array|string[]
     */
    public function options($actionID)
    {
        return [
            'limit',
            'offset',
        ];
    }

    /**
     * @return array
     */
    public function optionAliases()
    {
        return [
            'l' => 'limit',
            'o' => 'offset',
        ];
    }

    protected function configure()
    {
        $this->setName('vk:download')
            ->setDescription('Download music')
            ->setHelp('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln($this->checkAuth());
    }

    protected function checkAuth(): bool
    {
        if ($this->uid > 0) {
            return true;
        }

        $indexPageContent = $this->get()->getContents();
        $htmlParser = new AdvancedHtmlDom\AdvancedHtmlDom($indexPageContent);

        if ($htmlParser->find('#side_bar_inner #l_pr', 0)) {
            $this->setUid($indexPageContent);

            return true;
        }

        $ip_h = $htmlParser->find('input[name="ip_h"]', 0);
        $lg_h = $htmlParser->find('input[name="lg_h"]', 0);

        $location = $this->getAuthUrl($ip_h->value, $lg_h->value);

        if ($location) {
            $indexPageContent = $this->get($location)->getContents();
            $this->setUid($indexPageContent);

            return true;
        }

        return false;
    }

    protected function setUid($content): void
    {
        if (!preg_match('~\s+id\s*:[^\d]*(\d+)\s*,~', $content, $matches)) {
            preg_match('~"uid"\s*:[^\d]*(\d+)~', $content, $matches);
        }

        $this->uid = end($matches);
    }
}
