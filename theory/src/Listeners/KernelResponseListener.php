<?php

namespace App\Listeners;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class KernelResponseListener {

    public function onKernelResponse(ResponseEvent $event) {
        $response = new Response('Doot');
        $event->setResponse($response);
    }
}