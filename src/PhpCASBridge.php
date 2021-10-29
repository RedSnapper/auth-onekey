<?php

namespace RedSnapper\OneKey;

use phpCAS;
use Psr\Log\LoggerInterface;

class PhpCASBridge
{

    public function setClient(
      string $server_version,
      string $server_hostname,
      int $server_port,
      string $server_uri,
      bool $changeSessionID = true,
      \SessionHandlerInterface $sessionHandler = null
    ): void {
        phpCAS::client(
          $server_version,
          $server_hostname,
          $server_port,
          $server_uri,
          $changeSessionID,
          $sessionHandler
        );
    }

    public function setVerbose()
    {
        phpCAS::setVerbose(true);
        return $this;
    }

    public function forceAuthentication(): bool
    {
        return phpCAS::forceAuthentication();
    }

    public function setLogger(LoggerInterface $logger)
    {
        phpCAS::setLogger($logger);
        return $this;
    }

    public function getAttributes()
    {
        return phpCAS::getAttributes();
    }

    public function setNoClearTicketsFromUrl():self
    {
        phpCAS::setNoClearTicketsFromUrl();
        return $this;
    }

    public function setNoCasServerValidation():self
    {
        phpCAS::setNoCasServerValidation();
        return $this;
    }
}