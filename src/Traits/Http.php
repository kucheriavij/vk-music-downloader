<?php

namespace App\Traits;

use App\Service\Params;
use GuzzleHttp;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Trait Http
 *
 * @package App\Traits
 */
trait Http
{
    /**
     * @see http://docs.guzzlephp.org/en/latest/request-options.html#proxy
     *
     * @var array|string|null
     */
    protected $proxy = null;

    /**
     * @var ContainerInterface
     */
    protected $container = null;

    /**
     * @var GuzzleHttp\Cookie\FileCookieJar
     */
    protected $cookies;

    /**
     * @param $method
     * @param string $url
     * @param array $args
     * @return GuzzleHttp\Psr7\Response
     * @throws GuzzleHttp\Exception\GuzzleException
     */
    protected function httpRequest($method, string $url, array $args): GuzzleHttp\Psr7\Response
    {
        $request = new GuzzleHttp\Client($this->requestOptions);

        if (null === $this->cookies) {
            $this->cookies = new GuzzleHttp\Cookie\FileCookieJar($this->getParam('app_cookie_store'));
        }

        return $request->request($method, $url, $args + [
            'cookies' => $this->cookies,
            'headers' => $this->defaultHeaders,
            'proxy' => $this->proxy,
        ]);
    }

    /**
     * @param string $url
     * @param array|null $data
     * @param array|null $headers
     * @return StreamInterface
     * @throws GuzzleHttp\Exception\GuzzleException
     */
    protected function httpPost(string $url, ?array $data = null, ?array $headers = []): StreamInterface
    {
        $response = $this->httpRequest('post', $url, [
            'headers' => $headers + $this->defaultHeaders,
            'form_params' => $data,
        ]);

        return $response->getBody();
    }

    /**
     * @param string $url
     * @param array|null $headers
     * @return StreamInterface
     * @throws GuzzleHttp\Exception\GuzzleException
     */
    protected function httpGet(string $url = '', ?array $headers = []): StreamInterface
    {
        /**
         * @var $response ResponseInterface
         */
        $response = $this->httpRequest('get', $url, [
            'headers' => $headers + $this->defaultHeaders,
        ]);

        return $response->getBody();
    }

    /**
     * @return array
     */
    protected function getCookiesAsArray(): array
    {
        if (null === $this->cookies) {
            return [];
        }

        $cookies = [];

        foreach ($this->cookies as $item) {
            $cookies[$item->getName()] = $item->getValue();
        }

        return $cookies;
    }

    /**
     * @param string $ip_h
     * @param string $lg_h
     * @return array|bool|null
     * @throws GuzzleHttp\Exception\GuzzleException
     */
    protected function getAuthUrl(string $ip_h, string $lg_h)
    {
        $checkAuth = $this->httpRequest('post', 'http://login.vk.com/?act=login', [
            'form_params' => [
                'act' => 'login',
                'role' => 'al_frame',
                '_origin' => $this->requestOptions['base_uri'] ?? 'http://vk.com',
                'ip_h' => $ip_h,
                'lg_h' => $lg_h,
                'email' => $this->getParam('login'),
                'pass' => $this->getParam('pass'),
                'utf8' => 1,
                'expire' => '',
                'recaptcha' => '',
                'captcha_sid' => '',
                'captcha_key' => '',
            ],
            'allow_redirects' => false
        ]);

        $location = $checkAuth->getHeader('Location');
        $location = $location[0] ?? false;

        if (!$location || strpos($location, '__q_hash') === false) {
            return null;
        }

        return $location;
    }
}
