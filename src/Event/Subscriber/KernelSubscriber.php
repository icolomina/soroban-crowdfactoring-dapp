<?php

namespace App\Event\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;

class KernelSubscriber implements EventSubscriberInterface{

    public function __construct(
        private readonly RouterInterface $router
    ){}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onException'
        ];
    }

    public function onException(ExceptionEvent $event): void 
    {
        /*$exception = $event->getThrowable();
        if(str_contains($exception->getMessage(), 'Full authentication is required')) {
            $event->setResponse(new RedirectResponse($this->router->generate('get_login')));
        }*/
    }
}