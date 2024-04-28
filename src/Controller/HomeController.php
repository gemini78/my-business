<?php 

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController {

    #[Route('/', name: 'homepage')]
    public function homepage(ProductRepository $productRepository) {
        $products = $productRepository->findBy([],[],3);
        return $this->render('home.html.twig', [
            "products" => $products
        ]);
    }
}