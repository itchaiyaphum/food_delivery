<?php

class Base_app
{
    public $app = null;

    public function __construct($app = null)
    {
        $this->app = $app;
    }
}
