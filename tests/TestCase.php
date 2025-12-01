<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase; 

abstract class TestCase extends BaseTestCase
{
    // 🔑 CLAVE 1: Usa RefreshDatabase para migrar la base de datos de prueba antes de cada test.
    use CreatesApplication, RefreshDatabase; 

    // 🔑 CLAVE 2: Ejecuta el DatabaseSeeder (y por lo tanto RolesSeeder, UserStatusSeeder, etc.) antes de cada test.
    protected bool $seed = true; 
}