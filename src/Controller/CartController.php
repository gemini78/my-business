<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use phpDocumentor\Reflection\Types\ArrayKey;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    #[Route('/cart/add/{id}', name: 'cart_add', requirements:['id'=>"\d+"])]
    public function add($id, ProductRepository $productRepository, SessionInterface $session): Response
    {
        $product = $productRepository->find($id);

        if(!$product) {
            throw $this->createNotFoundException("Le produit $id n'existe pas !");
        }

        $cart = $session->get('cart', []);

        if(array_key_exists($id, $cart)) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }
        $session->set('cart', $cart);

        /** @var FlashBag */
        $flashbag = $session->getBag("flashes");

        $flashbag->add('success', "Le produit a bien été ajoute au panier");

        return $this->redirectToRoute('product_show', [
            'category_slug' => $product->getCategory()->getSlug(),
            'slug' => $product->getSlug(),
        ]);
    }
}
