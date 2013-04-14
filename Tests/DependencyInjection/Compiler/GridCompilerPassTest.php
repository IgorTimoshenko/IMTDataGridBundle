<?php

/*
 * This file is part of the IMTDataGridBundle package.
 *
 * (c) Igor M. Timoshenko <igor.timoshenko@i.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IMT\DataGridBundle\Tests\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Definition;

use IMT\DataGridBundle\DependencyInjection\Compiler\GridCompilerPass;

/**
 * @author Igor Timoshenko <igor.timoshenko@i.ua>
 */
class GridCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers IMT\DataGridBundle\DependencyInjection\Compiler\GridCompilerPass::process
     */
    public function testProcessWithNonExistingDefinition()
    {
        $definition = new Definition('DefinitionClass');

        $containerBuilder = $this->getContainerBuilderMock($definition, false);

        $gridCompilerPass = new GridCompilerPass();
        $gridCompilerPass->process($containerBuilder);

        $this->assertCount(0, $definition->getMethodCalls());
    }

    /**
     * @covers IMT\DataGridBundle\DependencyInjection\Compiler\GridCompilerPass::process
     */
    public function testProcess()
    {
        $definition = new Definition('DefinitionClass');

        $containerBuilder = $this->getContainerBuilderMock($definition, true);

        $gridCompilerPass = new GridCompilerPass();
        $gridCompilerPass->process($containerBuilder);

        $methodCalls = $definition->getMethodCalls();

        $this->assertCount(1, $methodCalls);
        $this->assertEquals('add', $methodCalls[0][0]);
    }

    /**
     * @param  Definition                               $definition
     * @param  boolean                                  $hasDefinition
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getContainerBuilderMock(
        Definition $definition,
        $hasDefinition = false
    ) {
        $containerBuilder = $this
            ->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $containerBuilder
            ->expects($this->once())
            ->method('hasDefinition')
            ->with($this->equalTo('imt_data_grid.registry'))
            ->will($this->returnValue($hasDefinition));

        $taggedServiceIds = array('grid' => array(array('alias' => 'grid')));

        $containerBuilder
            ->expects($this->any())
            ->method('findTaggedServiceIds')
            ->with($this->equalTo('imt_data_grid.grid'))
            ->will($this->returnValue($taggedServiceIds));
        $containerBuilder
            ->expects($this->any())
            ->method('getDefinition')
            ->with($this->equalTo('imt_data_grid.registry'))
            ->will($this->returnValue($definition));

        return $containerBuilder;
    }
}
