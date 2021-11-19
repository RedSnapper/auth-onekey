<?php

namespace RedSnapper\OneKey;

use Illuminate\Session\NullSessionHandler;
use Psr\Log\LoggerInterface;

class OneKeyProvider
{

    private PhpCASBridge $phpCASBridge;

    private const SERVER_PORT = 443;
    private const SERVER_URI = 'sso';

    private array $config;

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

    private function parseConfig(array $config): array
    {
        return array_merge([
          'debug' => false,
          'live' => true,
        ], $config);
    }

    private function getHostName(): string
    {
        return $this->config['live'] ? 'www.owa-secure.com' : 'www.rowa-secure.com';
    }
}