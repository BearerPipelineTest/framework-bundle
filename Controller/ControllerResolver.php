<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\FrameworkBundle\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\HttpKernel\Controller\ContainerControllerResolver;

/**
 * ControllerResolver.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ControllerResolver extends ContainerControllerResolver
{
    protected $parser;

    /**
     * Constructor.
     *
     * @param ContainerInterface   $container A ContainerInterface instance
     * @param ControllerNameParser $parser    A ControllerNameParser instance
     * @param LoggerInterface      $logger    A LoggerInterface instance
     */
    public function __construct(ContainerInterface $container, ControllerNameParser $parser, LoggerInterface $logger = null)
    {
        $this->parser = $parser;

        parent::__construct($container, $logger);
    }

    /**
     * {@inheritdoc}
     */
    protected function createController($controller)
    {
        if (false === strpos($controller, '::') && 2 === substr_count($controller, ':')) {
            // controller in the a:b:c notation then
            $controller = $this->parser->parse($controller);
        }

        return parent::createController($controller);
    }

    /**
     * {@inheritdoc}
     */
    protected function instantiateController($class)
    {
        $controller = parent::instantiateController($class);

        if ($controller instanceof ContainerAwareInterface) {
            $controller->setContainer($this->container);
        }

        return $controller;
    }
}
