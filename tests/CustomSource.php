<?php

namespace Ushahidi\Tests;

use Ushahidi\DataSource\Contracts\IncomingDataSource;

class CustomSource implements IncomingDataSource
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function fetch($limit = false)
    {
    }

    public function getName()
    {
    }

    public function getId()
    {
    }

    public function getServices()
    {
    }

    public function getOptions()
    {
    }

    public function getInboundFields()
    {
    }

    public function getInboundFormId()
    {
    }

    public function getInboundFieldMappings()
    {
    }

    public function isUserConfigurable()
    {
    }
}
