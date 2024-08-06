<?php

namespace App\Event\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

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
        $exception = $event->getThrowable();
        
        if($exception->getPrevious() instanceof ValidationFailedException) {
            $errors = [];
            $violations = $exception->getPrevious()->getViolations();
            foreach($violations as $violation) {
                $errors[] = ['label' => $violation->getPropertyPath(), 'msg' => $violation->getMessage()];
            }
            
            $event->setResponse(new JsonResponse($errors, 422));
        }
    }
}