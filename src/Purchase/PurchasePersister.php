<?php

namespace App\Purchase;

use App\Cart\CartService;
use DateTime;
use DateTimeImmutable;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class PurchasePersister
{
    public function __construct(protected CartService $cartService, protected Security $security, protected EntityManagerInterface $em)
    {
    }

    public function storePurchase(Purchase $purchase)
    {
        $purchase->setUser($this->security->getUser())
            ->setPurchaseAt(DateTimeImmutable::createFromMutable(new DateTime()))
            ->setTotal($this->cartService->getTotal());

        $this->em->persist($purchase);

        foreach ($this->cartService->getDetailedCartItems() as $cartItem) {
            $purchaseItem = new PurchaseItem();
            $purchaseItem->setPurchase($purchase)
                ->setProduct($cartItem->product)
                ->setProductName($cartItem->product->getName())
                ->setProductPrice($cartItem->product->getPrice())
                ->setQuantity($cartItem->qty)
                ->setTotal($cartItem->getTotal());

            $this->em->persist($purchaseItem);
        }

        $this->em->flush();
    }
}
