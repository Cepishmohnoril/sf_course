<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class VideoCreatedSubscriber implements EventSubscriberInterface
{
    public function onVideoCreatedEvent($event)
    {
        dump($event->video);
    }

    public static function getSubscribedEvents()
    {
        return [
            'video.created.event' => 'onVideoCreatedEvent',
        ];
    }
}
