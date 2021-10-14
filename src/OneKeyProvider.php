<?php

namespace RedSnapper\OneKey;

use Psr\Log\LoggerInterface;

class OneKeyProvider
{
    private PhpCASBridge $phpCASBridge;

    private const SERVER_HOSTNAME = 'www.rowa-secure.com';
    private const SERVER_PORT = 443;
    private const SERVER_URI = 'sso';

    private array $config;

    public function __construct(PhpCASBridge $phpCASBridge, array $config=[])
    {
        $this->phpCASBridge = $phpCASBridge;

        $this->config = $this->parseConfig($config);

        $this->phpCASBridge->setClient(
          "S1",
          self::SERVER_HOSTNAME,
          self::SERVER_PORT,
          self::SERVER_URI,
          false,
          session()->getHandler()
        );

        if ($this->config['debug']) {
            $this->phpCASBridge->setVerbose();
            $this->phpCASBridge->setLogger(app()->make(LoggerInterface::class));
        }


    }

    public function redirect()
    {
        return redirect()->to($this->config['callback_url']);
    }

    public function user(): OneKeyUser
    {

        $this->phpCASBridge->setNoCasServerValidation();

        $this->phpCASBridge->forceAuthentication();

        return new OneKeyUser($this->phpCASBridge->getAttributes());
    }

    private function parseConfig(array $config): array
    {
        return array_merge([
          'debug' => false,
          'callback_url' => ''
        ], $config);
    }

}