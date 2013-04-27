<?php

/*
 * This file is part of the IMTDataGridBundle package.
 *
 * (c) Igor M. Timoshenko <igor.timoshenko@i.ua>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IMT\DataGridBundle\HttpFoundation;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

use IMT\DataGrid\HttpFoundation\RequestInterface;

/**
 * This class represents the data grid request
 *
 * @author Igor Timoshenko <igor.timoshenko@i.ua>
 */
class Request implements RequestInterface
{
    /**
     * @var SymfonyRequest
     */
    private $symfonyRequest;

    /**
     * The constructor method
     *
     * @param SymfonyRequest $symfonyRequest
     */
    public function __construct(SymfonyRequest $symfonyRequest)
    {
        $this->symfonyRequest = $symfonyRequest;
    }

    /**
     * {@inheritDoc}
     */
    public function getFilters()
    {
        $filters = json_decode($this->symfonyRequest->get('filters'), true);

        !is_array($filters) && $filters = array();

        return $filters;
    }

    /**
     * {@inheritDoc}
     */
    public function getLimit()
    {
        return $this->symfonyRequest->get('rows', 0);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return $this->symfonyRequest->get('sord', null);
    }

    /**
     * {@inheritDoc}
     */
    public function getPage()
    {
        return $this->symfonyRequest->get('page', 0);
    }

    /**
     * {@inheritDoc}
     */
    public function getSort()
    {
        return $this->symfonyRequest->get('sidx', null);
    }

    /**
     * {@inheritDoc}
     */
    public function isSearch()
    {
        return $this->symfonyRequest->get('_search', false);
    }
}
