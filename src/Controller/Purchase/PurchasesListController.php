<?php 

namespace App\Controller\Purchase;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Twig\Environment;

class PurchasesListController extends AbstractController
{
    public function __construct(protected Security $security, protected RouterInterface $router, protected Environment $twig)
    {}

    #[Route('/purchases', name: 'purchases_index')]
    public function index()
    {
       // 1_ Personne connectée sinon redirection homepage -> security
       /** @var User */
       $user = $this->security->getUser();

       if(!$user){
            throw new AccessDeniedException("Vous devez être connecté pour acceder à vos commandes");
       }
       // 2_ Qui est connecté -> security
       // 3_ Passé le user à Twig -> Environment
       $html = $this->twig->render('purchase/index.html.twig', [
            'purchases' => $user->getPurchases()
       ]);
       return new Response($html);
    }
}