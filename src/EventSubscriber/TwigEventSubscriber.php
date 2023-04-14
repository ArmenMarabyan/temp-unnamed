<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;

class TwigEventSubscriber implements EventSubscriberInterface
{

    public function __construct(private Environment $twig)
    {
    }

    public function onControllerEvent(ControllerEvent $event): void
    {
        $menuItems = [ //todo replace from DB
            'home' => '/',
            'blog' => '/blog',
            'Admin' => '/admin',
        ];

        $this->twig->addGlobal('menu_items', $menuItems);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ControllerEvent::class => 'onControllerEvent',
        ];
    }
}
