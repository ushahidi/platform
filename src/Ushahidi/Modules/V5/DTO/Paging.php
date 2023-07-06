<?php

namespace Ushahidi\Modules\V5\DTO;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Paging
{
    private $limit;
    private $page;
    private $order_by;
    private $order;

    public function __construct(
        Request $request,
        $default_sort_by = null,
        $default_order = null,
        $default_limit = null,
        $default_page = 1
    ) {
        if ($default_sort_by === null) {
            $default_sort_by = config('paging.default_sort_by');
        }
        if ($default_order === null) {
            $default_order = config('paging.default_order');
        }
        if ($default_limit === null) {
            $default_limit = config('paging.default_limit');
        }


        $limit = $request->query('limit', $default_limit);
        $this->limit = $limit ? $limit : config('paging.default_laravel_pageing_limit');
        $this->page = $request->query('page', $default_page);
        $this->order_by = $request->query('orderby', $default_sort_by);
        $this->order = $request->query('order', $default_order);
    }

    public static function fromRequest(
        Request $request,
        $default_sort_by = null,
        $default_order = null,
        $default_limit = null,
        $default_page = 1
    ) {
        return new self($request, $default_sort_by, $default_order, $default_limit, $default_page);
    }


    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getSkip(): int
    {
        return $this->limit * ($this->page - 1);
    }

    public function getOrderBy(): string
    {
        return $this->order_by;
    }

    public function getOrder(): string
    {
        return $this->order;
    }
}
