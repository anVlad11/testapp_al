<?php
/**
 * Created by PhpStorm.
 * User: anvlad11
 * Date: 31.03.2018
 * Time: 17:57
 */

namespace Application\Controller\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;
use Application\Controller\IndexController;

class IndexControllerFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, Array $options = null) {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        return new IndexController($entityManager);
    }

}