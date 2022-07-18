<?php

namespace Ushahidi\DataSource\Concerns;

trait MapsInboundFields
{
    public function getInboundFormId()
    {
        return isset($this->config['form_id']) ? $this->config['form_id'] : false;
    }

    public function getInboundFieldMappings()
    {
        return isset($this->config['inbound_fields']) ? $this->config['inbound_fields'] : [];
    }
}
