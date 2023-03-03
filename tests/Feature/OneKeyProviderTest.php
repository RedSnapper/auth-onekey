<?php

namespace RedSnapper\OneKey\Tests\Feature;

use Mockery\MockInterface;
use RedSnapper\OneKey\Exception\MissingServiceBaseUrlException;
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

        $bridge->shouldReceive('getAttributes')->once()->andReturn(
            $rawData = [
                'UID'                    => '1234567890',
                'firstname'              => 'John',
                'name'                   => 'Doe',
                'email'                  => 'user@example.com',
                'city'                   => 'London',
                'profession'             => 'Doctor',
                'professionalPhone'      => '1234512341234',
                'usertype'               => 'PS',
                'Cegedim_security_level' => '4',
            ]
        );

        $provider = new OneKeyProvider($bridge, ['service-base-url' => 'https://test.com']);

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

    /** @test */
    public function url_used_for_cas_client_changes_depending_on_whether_we_are_using_a_test_environment()
    {
        $bridge = $this->mock(PhpCASBridge::class);
        $bridge->shouldReceive('setClient')->once()->withSomeOfArgs("www.rowa-secure.com");

        $provider = new OneKeyProvider($bridge, ['live' => false, 'service-base-url' => 'https://test.com']);
    }

    /** @test */
    public function url_used_for_cas_client_changes_depending_on_whether_we_are_using_a_live_environment()
    {
        $bridge = $this->mock(PhpCASBridge::class);
        $bridge->shouldReceive('setClient')->once()->withSomeOfArgs("www.owa-secure.com");

        $provider = new OneKeyProvider($bridge, ['live' => true, 'service-base-url' => 'https://test.com']);
    }

    /** @test */
    public function service_base_url_defaults_base_request_url_if_not_provided_in_config()
    {
        $bridge = $this->mock(PhpCASBridge::class);

        $requestMock = $this->partialMock(\Illuminate\Http\Request::class, function (MockInterface $mock) {
            $mock->shouldReceive('getSchemeAndHttpHost')->andReturn('https://onekeytest.com')->once();
        });

        app()->bind('request', fn() => $requestMock);

        $bridge->shouldReceive('setClient')->once()->withSomeOfArgs('https://onekeytest.com');
        $provider = new OneKeyProvider($bridge);
    }

    /** @test */
    public function service_base_url_is_retrieved_from_config_if_set()
    {
        $bridge = $this->mock(PhpCASBridge::class);

        $bridge->shouldReceive('setClient')->once()->withSomeOfArgs('https://abc.com');

        $provider = new OneKeyProvider($bridge, ['service-base-url' => 'https://abc.com']);
    }

    /** @test */
    public function exception_is_thrown_if_service_base_url_not_provided_and_default_cannot_be_resolved()
    {
        $this->expectException(MissingServiceBaseUrlException::class);

        $requestMock = $this->partialMock(\Illuminate\Http\Request::class, function (MockInterface $mock) {
            $mock->shouldReceive('getSchemeAndHttpHost')->andReturn('')->once();
        });

        app()->bind('request', fn() => $requestMock);

        $bridge = $this->mock(PhpCASBridge::class);
        $provider = new OneKeyProvider($bridge);
    }
}