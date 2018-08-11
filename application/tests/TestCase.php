<?php

use League\FactoryMuffin\FactoryMuffin;

class TestCase extends Illuminate\Foundation\Testing\TestCase {

    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';
    protected static $fm;

    /**
     * TestCase constructor.
     */
    public function __construct()
    {
        $this->baseUrl = env('BASE_URL', $this->baseUrl);
    }

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';
        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        return $app;
    }

    public function setUp()
    {
        try {
            parent::setUp();
            \Artisan::call('migrate');
            \Artisan::call('db:seed');
            static::$fm = new FactoryMuffin();
            static::$fm->loadFactories(__DIR__.'/factories');
        } catch(Exception $e){
            die('Error:' . $e->getMessage() . "\n" . $e->getTraceAsString());
        }
    }


    public function tearDown()
    {
        static::$fm->deleteSaved();
        Mockery::close();
        //Artisan::call('migrate:reset');
        parent::tearDown();
    }

    public function mock($class)
    {
        $mock = Mockery::mock($class);
        $this->app->instance($class, $mock);
        return $mock;
    }

}
