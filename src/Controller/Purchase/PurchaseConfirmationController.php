<?php

namespace App\Controller\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Form\CartConfirmationType;
use App\Purchase\PurchasePersister;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PurchaseConfirmationController extends AbstractController
{
    public function __construct(
        protected CartService $cartService,
        protected EntityManagerInterface $em,
        protected PurchasePersister $purchasePersister
    ) {
    }

    #[Route('/purchase/confirm', name: 'purchase_confirm')]
    #[IsGranted('ROLE_USER', message: "Vous devez être connecté pour confirmer une commande")]
    public function confirm(Request $request)
    {
        $form = $this->createForm(CartConfirmationType::class);

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            $this->addFlash('warning', "Vous devez remplir le formulaire de confirmation");
            return $this->redirectToRoute('cart_show');
        }

        $cartItems = $this->cartService->getDetailedCartItems();
        if (count($cartItems) === 0) {
            $this->addFlash('warning', "Vous ne pouvez pas confirmer une commande avec un panier vide");
            return $this->redirectToRoute('cart_show');
        }

        /** @var Purchase */
        $purchase = $form->getData();

        $this->purchasePersister->storePurchase($purchase);

        $this->cartService->empty();

        $this->addFlash('success', "La commande a bien été enregistrée");
        return $this->redirectToRoute('purchases_index');
    }
}
