<?php

declare(strict_types=1);

namespace Application\Factory;

use Application\Controller\IndexController;
use Laminas\Db\Adapter\AdapterInterface;
use Psr\Container\ContainerInterface;

class IndexControllerFactory
{
    public function __invoke(ContainerInterface $container): IndexController
    {
        return new IndexController(
            $container->get(AdapterInterface::class)
        );
    }
}
