<?php

namespace App\EventDispatcher;

use App\Event\PurchaseSuccessEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PurchaseSuccessEmailSubscriber implements EventSubscriberInterface
{
    public function __construct(private LoggerInterface $logger)
    {
    }
    public static function getSubscribedEvents(): array
    {
        return [
            'purchase.success' => [
                ['sendSuccessEmail']
            ]
        ];
    }

    public function sendSuccessEmail(PurchaseSuccessEvent $purchaseSuccessEvent)
    {
        // Pour simuler l'envoi d'un mail lors d'une prise de commande
        $this->logger->info("Email envoyé pour la commande N° : " . $purchaseSuccessEvent->getPurchase()->getId());
    }
}
