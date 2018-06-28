<?php

namespace App\Service;


use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Params
 *
 * @package App\Service
 */
class Params
{
    /**
     * @var ContainerInterface
     */
    protected $container = null;

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getParameter($key)
    {
        return $this->container->getParameter($key);
    }

    /**
     * @param mixed ...$args
     * @return object
     */
    public function get(...$args)
    {
        return $this->container->get(...$args);
    }
}
