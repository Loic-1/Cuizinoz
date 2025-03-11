<?php

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HoneyPotSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $logger;

    private RequestStack $requestStack;

    public function __construct(LoggerInterface $logger, RequestStack $requestStack)
    {
        $this->logger = $logger;
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => 'checkHoneyJar'
        ];
    }

    public function checkHoneyJar(FormEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request) {
            return;
        }

        $data = $event->getData();

        if (!array_key_exists('phone', $data) || !array_key_exists('faxNumber', $data)) {
            throw new HttpException(400, "Form modified");
        }

        [
            'phone' => $phone,
            'faxNumber' => $faxnumber,
        ] = $data;

        if ($phone !== "" || $faxnumber !== "") {
            $this->logger->info("Potentielle tentative de spam par un robot spammeur ayant l'adresse IP suivante: " . $request->getClientIp() . " , le champ phone contenait " . $phone . " et le champ faxNumber contenait " . $faxnumber . ".");
            throw new HttpException(403, "bot!!");
        }
    }
}
