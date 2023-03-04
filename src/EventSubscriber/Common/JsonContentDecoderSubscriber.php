<?php

namespace App\EventSubscriber\Common;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class JsonContentDecoderSubscriber implements EventSubscriberInterface
{
    private const APPLICATION_JSON = 'json';

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $content = $request->getContent();

        if (
            $content &&
            $request->getContentTypeFormat() === self::APPLICATION_JSON &&
            in_array($request->getMethod(), [Request::METHOD_POST, Request::METHOD_PUT, Request::METHOD_PATCH])
        ) {
            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $event->setResponse(new Response('Bad json', Response::HTTP_BAD_REQUEST));

                return;
            }

            $request->request = new ParameterBag($data);
        }
    }
}