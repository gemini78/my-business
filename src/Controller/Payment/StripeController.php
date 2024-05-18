<?php

namespace App\Controller\Payment;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Event\PurchaseSuccessEvent;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class StripeController extends AbstractController
{
    #[Route('/payment/success/{id}', name: 'payment_success', requirements: ['id' => "\d+"])]
    #[IsGranted('ROLE_USER', message: "Vous devez être connecté pour effectuer un paiement")]
    public function mySuccess($id, PurchaseRepository $purchaseRepository, EntityManagerInterface $em, CartService $cartService, EventDispatcherInterface $dispatcher)
    {
        /** @var Purchase */
        $purchase = $purchaseRepository->find($id);
        if (
            !$purchase ||
            ($purchase && $purchase->getUser() !== $this->getUser()) ||
            ($purchase && $purchase->getStatus() === Purchase::STATUS_PAID)
        ) {
            $this->addFlash('warning', "Commande invalide");
            return $this->redirectToRoute('purchases_index');
        }

        $purchase->setStatus(Purchase::STATUS_PAID);
        $em->flush();
        $cartService->empty();

        // Lancer un evenement qui permet aux autres developpeurs de réagir à la prise de commande
        $purchaseEvent = new PurchaseSuccessEvent($purchase);
        $dispatcher->dispatch($purchaseEvent, 'purchase.success');

        $this->addFlash('success', "La commande a été payée et confirmée !");
        return $this->redirectToRoute('purchases_index');
    }

    #[Route('/payment/cancel', name: 'payment_cancel')]
    #[IsGranted('ROLE_USER', message: "Vous devez être connecté pour faire un paiement")]
    public function myCancel()
    {
        $this->addFlash('danger', "Impossible de payer cette commande");
        return $this->redirectToRoute('purchases_index');
    }
}
