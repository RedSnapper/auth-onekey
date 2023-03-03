<?php

namespace RedSnapper\OneKey;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Session\NullSessionHandler;
use Illuminate\Support\Arr;
use Psr\Log\LoggerInterface;
use RedSnapper\OneKey\Exception\MissingServiceBaseUrlException;

class OneKeyProvider
{

    private PhpCASBridge $phpCASBridge;

    private const SERVER_PORT = 443;
    private const SERVER_URI = 'sso';

    private array $config;

    /**
     * @throws MissingServiceBaseUrlException
     * @throws BindingResolutionException
     */
    public function __construct(PhpCASBridge $phpCASBridge, array $config = [])
    {
        $this->phpCASBridge = $phpCASBridge;

        if (PHP_SAPI !== 'cli') {
            // We don't want to create unnecessary cookies by using the session
            ini_set('session.use_cookies', "0");
        }

        $this->config = $this->parseConfig($config);

        $this->phpCASBridge->setClient(
            "S1",
            $this->getHostName(),
            self::SERVER_PORT,
            self::SERVER_URI,
            $this->config['service-base-url'],
            false,
            new NullSessionHandler() // We don't want to a use a session at all because we are managing our own sessions
        );

        if ($this->config['debug']) {
            $this->phpCASBridge->setVerbose();
            $this->phpCASBridge->setLogger(app()->make(LoggerInterface::class));
        }
    }

    public function user(): OneKeyUser
    {
        $this->phpCASBridge->setNoCasServerValidation()

            // This prevents redirecting after successful authentication back to the callback url
            // We are already handling the redirect after successful authentication, and it also means that we don't need
            // to store the user in a php session
            ->setNoClearTicketsFromUrl()

            // This wll redirect the user to onekey when there is no ticket in the url and otherwise retrieve the user
            // from the ticket if it is valid
            ->forceAuthentication();

        return new OneKeyUser($this->phpCASBridge->getAttributes());
    }

    /**
     * @throws MissingServiceBaseUrlException
     */
    private function parseConfig(array $config): array
    {
        if (is_null(Arr::get($config, 'service-base-url'))) {
            $config['service-base-url'] = $this->getDefaultServiceBaseUrl();
        }

        return array_merge([
            'debug' => false,
            'live'  => true,
        ], $config);
    }

    /**
     * @throws MissingServiceBaseUrlException
     */
    private function getDefaultServiceBaseUrl(): string
    {
        $url = request()->getSchemeAndHttpHost();

        if (empty($url)) {
            throw new MissingServiceBaseUrlException();
        }

        return $url;
    }

    private function getHostName(): string
    {
        return $this->config['live'] ? 'www.owa-secure.com' : 'www.rowa-secure.com';
    }
}