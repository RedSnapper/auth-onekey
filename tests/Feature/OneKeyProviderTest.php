<?php

namespace RedSnapper\OneKey\Tests\Feature;

use Config;
use Illuminate\Support\Arr;
use RedSnapper\OneKey\OneKeyProvider;
use RedSnapper\OneKey\PhpCASBridge;
use RedSnapper\OneKey\Tests\TestCase;

class OneKeyProviderTest extends TestCase
{
    /** @test */
    public function it_can_retrieve_a_user_from_one_key()
    {
        $bridge = $this->mock(PhpCASBridge::class);
        $bridge->shouldReceive('setClient')->once();
        $bridge->shouldReceive('setNoCasServerValidation->setNoClearTicketsFromUrl->forceAuthentication')->once();

        $bridge->shouldReceive('getAttributes')->once()->andReturn($rawData = [
            'UID' => '1234567890',
            'firstname' => 'John',
            'name' => 'Doe',
            'email'=> 'user@example.com',
            'city' => 'London',
            'profession' => 'Doctor',
            'professionalPhone' => '1234512341234',
            'usertype'=> 'PS',
            'Cegedim_security_level' => '4'
        ]);

        $provider = new OneKeyProvider($bridge);

        $user = $provider->user();

        $this->assertEquals('1234567890', $user->getId());
        $this->assertEquals('user@example.com', $user->getEmail());
        $this->assertEquals('John', $user->getFirstName());
        $this->assertEquals('Doe', $user->getLastName());
        $this->assertEquals('John Doe', $user->getFullName());
        $this->assertEquals('London', $user->getCity());
        $this->assertEquals('Doctor', $user->getProfession());
        $this->assertEquals('1234512341234', $user->getPhone());
        $this->assertTrue($user->isHCP());
        $this->assertEquals('4', $user->trustLevel());

        $this->assertEquals($rawData, $user->getRaw());
    }
}