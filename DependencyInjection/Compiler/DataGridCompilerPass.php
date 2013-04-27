<?php

/*
 * This file is part of the IMTDataGridBundle package.
 *
 * (c) Igor M. Timoshenko <igor.timoshenko@i.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IMT\DataGridBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This processes the tagged services with the name `imt_data_grid.data_grid`
 *
 * @author Igor Timoshenko <igor.timoshenko@i.ua>
 */
class DataGridCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('imt_data_grid.registry')) {
            return;
        }

        $taggedServices = $container
            ->findTaggedServiceIds('imt_data_grid.data_grid');

        $definition     = $container->getDefinition('imt_data_grid.registry');

        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall(
                    'add',
                    array(new Reference($id), $attributes['alias'])
                );
            }
        }
    }
}
