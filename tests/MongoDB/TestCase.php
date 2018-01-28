<?php
namespace Fathomminds\Rest\Tests\MongoDB;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Mockery;

abstract class TestCase extends PHPUnitTestCase
{
    protected $mockDatabase;
    protected $mockClient;

    public function __construct()
    {
        parent::__construct();
    }

    public function tearDown()
    {
        parent::tearDown();
        Mockery::close();
    }
}
