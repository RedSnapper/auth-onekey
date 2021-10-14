<?php

namespace RedSnapper\OneKey\Tests;

use RedSnapper\OneKey\OneKeyServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
  public function setUp(): void
  {
    parent::setUp();
    // additional setup
  }

  protected function getPackageProviders($app): array
  {
    return [
      OneKeyServiceProvider::class,
    ];
  }

  protected function getEnvironmentSetUp($app)
  {
    // perform environment setup
  }
}
