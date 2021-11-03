<?php

namespace App\Services;

use Psr\Log\LoggerInterface;

class MailService
{
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->logger->info('Mailer service initiated.');
    }

    public function canSend() {
        return (bool) rand(0, 1);
    }
}
