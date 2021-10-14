<?php

namespace RedSnapper\OneKey\Tests;

use RedSnapper\OneKey\OneKeyProvider;
use RedSnapper\OneKey\PhpCASBridge;

class OneKeyProviderTest extends TestCase
{
    /** @test */
    public function it_can_retrieve_user_from_one_key()
    {
        $bridge = $this->mock(PhpCASBridge::class);
        $bridge->shouldReceive('setClient')->once();
        $bridge->shouldReceive('setNoCasServerValidation')->once();
        $bridge->shouldReceive('forceAuthentication')->once();
        $bridge->shouldReceive('getAttributes')->once()->andReturn([
          'email'=> 'param@redsnapper.net'
        ]);

        $provider = new OneKeyProvider($bridge);

        $user = $provider->user();

        $this->assertEquals('param@redsnapper.net',$user->getEmail());


    }
}