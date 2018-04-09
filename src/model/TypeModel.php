<?php

namespace model;

use Symfony\Component\Config\Definition\Exception\Exception;

class TypeModel
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }
}
