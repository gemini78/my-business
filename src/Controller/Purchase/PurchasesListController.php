<?php 

namespace App\Controller\Purchase;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PurchasesListController extends AbstractController
{

    #[Route('/purchases', name: 'purchases_index')]
    #[IsGranted('ROLE_USER', message:'Vous devez être connecté pour acceder à vos commandes')]
    public function index()
    {
       // 1_ Personne connectée sinon redirection homepage -> security
       /** @var User */
       $user = $this->getUser();

       // 2_ Qui est connecté -> security
       // 3_ Passé le user à Twig -> Environment
       return $this->render('purchase/index.html.twig', [
            'purchases' => $user->getPurchases()
        ]);
    }
}