<?php

/*
 * This file is part of the IMTDataGridBundle package.
 *
 * (c) Igor M. Timoshenko <igor.timoshenko@i.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IMT\DataGridBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use IMT\DataGridBundle\DependencyInjection\Compiler\GridCompilerPass;

/**
 * This class adds a few conventions for DependencyInjection extensions and
 * Console commands
 *
 * @author Igor Timoshenko <igor.timoshenko@i.ua>
 */
class IMTDataGridBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new GridCompilerPass());
    }
}
