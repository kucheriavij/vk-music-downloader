<?php

namespace App\Service;


use Symfony\Component\DependencyInjection\ContainerInterface;

class Params
{
    /**
     * @var ContainerInterface
     */
    protected $container = null;

    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getParameter($key)
    {
        return $this->container->getParameter($key);
    }
    public function get(...$args)
    {
        return $this->container->get(...$args);
    }
}
