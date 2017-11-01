<?php

namespace Tests\Feature;

#use \Illuminate\Foundation\Testing\DatabaseMigrations;
use \Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class dbFunctionsTest extends \Illuminate\Foundation\Testing\TestCase
{
    protected $baseUrl = 'http://127.0.0.1';
    public function createApplication()
    {
        $app = require __DIR__ . '/../../bootstrap/app.php';
        $app->loadEnvironmentFrom(__DIR__ . '/../.env.testing');
        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        return $app;
    }

    function testMe()
    {
        $this->assertDatabaseHas('user', ['name' => 'rene']);
    }
}
