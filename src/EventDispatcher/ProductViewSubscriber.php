<?php

namespace App\EventDispatcher;

use App\Event\ProductViewEvent;
use App\Event\PurchaseSuccessEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductViewSubscriber implements EventSubscriberInterface
{
    public function __construct(private LoggerInterface $logger)
    {
    }
    public static function getSubscribedEvents(): array
    {
        return [
            'product.view' => [
                ['productView']
            ]
        ];
    }

    public function productView(ProductViewEvent $productViewEvent)
    {
        $product = $productViewEvent->getProduct();

        $text = sprintf("Le produit n°%s et nommé %s a été visité", $product->getId(), $product->getName());
        $this->logger->info($text);
    }
}
