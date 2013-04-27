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

use IMT\DataGridBundle\DependencyInjection\Compiler\DataGridCompilerPass;

/**
 * @author Igor Timoshenko <igor.timoshenko@i.ua>
 */
class DataGridCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers IMT\DataGridBundle\DependencyInjection\Compiler\DataGridCompilerPass::process
     */
    public function testProcessWithNonExistingDefinition()
    {
        $definition = new Definition('DefinitionClass');

        $containerBuilder = $this->getContainerBuilderMock($definition, false);

        $gridCompilerPass = new DataGridCompilerPass();
        $gridCompilerPass->process($containerBuilder);

        $this->assertCount(0, $definition->getMethodCalls());
    }

    /**
     * @covers IMT\DataGridBundle\DependencyInjection\Compiler\DataGridCompilerPass::process
     */
    public function testProcess()
    {
        $definition = new Definition('DefinitionClass');

        $containerBuilder = $this->getContainerBuilderMock($definition, true);

        $gridCompilerPass = new DataGridCompilerPass();
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
        $hasDefinition
    ) {
        $containerBuilder = $this
            ->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $containerBuilder
            ->expects($this->once())
            ->method('hasDefinition')
            ->with($this->equalTo('imt_data_grid.registry'))
            ->will($this->returnValue($hasDefinition));

        $taggedServiceIds = array(
            'data_grid' => array(array('alias' => 'data_grid')),
        );

        $containerBuilder
            ->expects($this->any())
            ->method('findTaggedServiceIds')
            ->with($this->equalTo('imt_data_grid.data_grid'))
            ->will($this->returnValue($taggedServiceIds));
        $containerBuilder
            ->expects($this->any())
            ->method('getDefinition')
            ->with($this->equalTo('imt_data_grid.registry'))
            ->will($this->returnValue($definition));

        return $containerBuilder;
    }
}
