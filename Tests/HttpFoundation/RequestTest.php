<?php

/*
 * This file is part of the IMTDataGridBundle package.
 *
 * (c) Igor M. Timoshenko <igor.timoshenko@i.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IMT\DataGridBundle\Tests\HttpFoundation;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

use IMT\DataGridBundle\HttpFoundation\Request;

/**
 * @author Igor Timoshenko <igor.timoshenko@i.ua>
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var SymfonyRequest
     */
    private $symfonyRequest;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->symfonyRequest = new SymfonyRequest();
        $this->request        = new Request($this->symfonyRequest);
    }

    /**
     * @covers IMT\DataGridBundle\HttpFoundation\Request::__construct
     */
    public function testConstruct()
    {
        $this->assertAttributeInstanceOf(
            'Symfony\Component\HttpFoundation\Request',
            'symfonyRequest',
            $this->request
        );
    }

    /**
     * @covers IMT\DataGridBundle\HttpFoundation\Request::getFilters
     */
    public function testGetFiltersReturnsEmptyArrayByDefault()
    {
        $this->assertEquals(array(), $this->request->getFilters());
    }

    /**
     * @covers IMT\DataGridBundle\HttpFoundation\Request::getFilters
     */
    public function testGetFiltersReturnsSpecifiedValue()
    {
        $filters = array(
            'groups' => 'groups',
            'op'     => 'op',
            'rules'  => 'rules',
        );

        $this->symfonyRequest->request->set('filters', json_encode($filters));

        $this->assertEquals($filters, $this->request->getFilters());
    }

    /**
     * @covers IMT\DataGridBundle\HttpFoundation\Request::getLimit
     */
    public function testGetLimitReturnsZeroByDefault()
    {
        $this->assertEquals(0, $this->request->getLimit());
    }

    /**
     * @covers IMT\DataGridBundle\HttpFoundation\Request::getLimit
     */
    public function testGetLimitReturnsSpecifiedValue()
    {
        $limit = 123;

        $this->symfonyRequest->request->set('limit', $limit);

        $this->assertEquals($limit, $this->request->getLimit());
    }

    /**
     * @covers IMT\DataGridBundle\HttpFoundation\Request::getOrder
     */
    public function testGetOrderReturnsNullByDefault()
    {
        $this->assertNull($this->request->getOrder());
    }

    /**
     * @covers IMT\DataGridBundle\HttpFoundation\Request::getOrder
     */
    public function testGetOrderReturnsSpecifiedValue()
    {
        $order = 'ASC';

        $this->symfonyRequest->request->set('sord', $order);

        $this->assertEquals($order, $this->request->getOrder());
    }

    /**
     * @covers IMT\DataGridBundle\HttpFoundation\Request::getPage
     */
    public function testGetPageReturnsZeroByDefault()
    {
        $this->assertEquals(0, $this->request->getPage());
    }

    /**
     * @covers IMT\DataGridBundle\HttpFoundation\Request::getPage
     */
    public function testGetPageReturnsSpecifiedValue()
    {
        $page = 1;

        $this->symfonyRequest->request->set('page', $page);

        $this->assertEquals($page, $this->request->getPage());
    }

    /**
     * @covers IMT\DataGridBundle\HttpFoundation\Request::getSort
     */
    public function testGetSortReturnsNullByDefault()
    {
        $this->assertNull($this->request->getSort());
    }

    /**
     * @covers IMT\DataGridBundle\HttpFoundation\Request::getSort
     */
    public function testGetSortReturnsSpecifiedValue()
    {
        $sort = 'field';

        $this->symfonyRequest->request->set('sidx', $sort);

        $this->assertEquals($sort, $this->request->getSort());
    }

    /**
     * @covers IMT\DataGridBundle\HttpFoundation\Request::isSearch
     */
    public function testIsSearchReturnsFalseByDefault()
    {
        $this->assertFalse($this->request->isSearch());
    }

    /**
     * @covers IMT\DataGridBundle\HttpFoundation\Request::isSearch
     */
    public function testIsSearchReturnsSpecifiedValue()
    {
        $isSearch = true;

        $this->symfonyRequest->request->set('_search', $isSearch);

        $this->assertEquals($isSearch, $this->request->isSearch());
    }
}
