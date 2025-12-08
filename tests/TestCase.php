<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase; 

// Clase base para los casos de prueba
abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase; 

    protected bool $seed = true; 
}
