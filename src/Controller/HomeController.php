<?php 

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController {

    #[Route('/', name: 'homepage')]
    public function index(EntityManagerInterface $em) {
        $repo = $em->getRepository(Product::class);
        $product = $repo->find(1);
        $product->setPrice(2500);
        $em->flush();
        dd($product);
        return $this->render('home.html.twig', []);
    }
}