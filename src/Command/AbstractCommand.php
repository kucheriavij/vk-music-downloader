<?php

namespace App\Command;


use App\Service\Params;
use App\Traits\Http;
use Bavix\AdvancedHtmlDom;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AbstractCommand
 *
 * @package App\Command
 */
abstract class AbstractCommand extends ContainerAwareCommand
{
    use Http;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * AbstractCommand constructor.
     *
     * @param string|null $name
     * @param ContainerInterface $container
     * @param LoggerInterface $logger
     */
    public function __construct(string $name = null, ContainerInterface $container, LoggerInterface $logger)
    {
        parent::__construct($name);
        $this->container = $container;
        $this->logger = $logger;
    }

    /**
     * @var bool
     */
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
        'verify' => false,
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
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function checkAuth(): bool
    {
        if ($this->uid > 0) {
            return true;
        }

        $indexPageContent = $this->httpGet('http://vk.com')->getContents();
        $htmlParser = new AdvancedHtmlDom\AdvancedHtmlDom($indexPageContent);

        if ($htmlParser->find('#side_bar_inner #l_pr', 0)) {
            $this->setUid($indexPageContent);
            $this->logger->info('Check auth success');

            return true;
        }

        $ip_h = $htmlParser->find('input[name="ip_h"]', 0);
        $lg_h = $htmlParser->find('input[name="lg_h"]', 0);

        $location = $this->getAuthUrl($ip_h->value, $lg_h->value);

        if ($location) {
            $indexPageContent = $this->httpGet($location)->getContents();
            $this->setUid($indexPageContent);
            $this->logger->info('Check auth success');

            return true;
        }

        $this->logger->error('Check auth error');

        return false;
    }

    /**
     * @param $content
     */
    protected function setUid($content): void
    {
        if (!preg_match('~\s+id\s*:[^\d]*(\d+)\s*,~', $content, $matches)) {
            preg_match('~"uid"\s*:[^\d]*(\d+)~', $content, $matches);
        }

        $this->uid = end($matches);
    }

    /**
     * @param $key
     * @return mixed
     * @throws \BadFunctionCallException
     */
    protected function getParam($key)
    {
        if (null === $this->container) {
            throw new \BadMethodCallException('Conteiner is null');
        }

        /**
         * @var $params Params
         */
        $params = $this->container->get('params')->get('params');

        return $params->getParameter($key);
    }

    /**
     * @param Console\Input\InputInterface $input
     * @param Console\Output\OutputInterface $output
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function login(Console\Input\InputInterface $input, Console\Output\OutputInterface $output): bool
    {
        $checkAuth = $this->checkAuth();
        $io = new SymfonyStyle($input, $output);

        if ($checkAuth) {
            $io->write('Login action success' . PHP_EOL);
            $this->logger->info('Login action success');

            return true;
        }

        $io->write('Login action failed' . PHP_EOL);
        $this->logger->error('Login action failed');

        return false;
    }
}
