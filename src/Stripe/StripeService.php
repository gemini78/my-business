<?php

namespace App\Stripe;

use App\Entity\Purchase;

class StripeService
{
    public function __construct(protected string $stripeSecretKey, protected string $domain)
    {
    }

    public function getPaymentIntent(Purchase $purchase)
    {
        $arrayProducts = [];

        foreach ($purchase->getPurchaseItems() as $purchaseItem) {
            $arrayProducts[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $purchaseItem->getProductName(),
                        'images' => [$purchaseItem->getProduct()->getMainPicture()]
                    ],
                    'unit_amount' => $purchaseItem->getProduct()->getPrice()
                ],
                'quantity' => $purchaseItem->getQuantity(),

            ];
        }

        \Stripe\Stripe::setApiKey($this->stripeSecretKey);

        $YOUR_DOMAIN = $this->domain;

        return \Stripe\Checkout\Session::create([
            'line_items' => [$arrayProducts],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/payment/success/' . $purchase->getId(),
            'cancel_url' => $YOUR_DOMAIN . '/payment/cancel',
        ]);
    }
}
