<?php

namespace App\Controller;

use App\Entity\Product;
use App\Event\ProductViewEvent;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductController extends AbstractController
{

    #[Route('/{slug}', name: 'product_category', priority: -1)]
    public function category($slug, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy(['slug' => $slug]);

        if (!$category) {
            throw $this->createNotFoundException("La page demandée n'existe pas");
        }

        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            'category' => $category,
        ]);
    }

    #[Route('/{category_slug}/{slug}', name: 'product_show', priority: -1)]
    public function show($slug, ProductRepository $productRepository, EventDispatcherInterface $dispatcher)
    {
        $product = $productRepository->findOneBy([
            'slug' => $slug
        ]);

        if (!$product) {
            throw $this->createNotFoundException("Le produit demandé n'existe pas");
        }

        $productView = new ProductViewEvent($product);
        $dispatcher->dispatch($productView, 'product.view');

        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/admin/product/{id}/edit', name: 'product_edit')]
    public function edit($id, Request $request, ProductRepository $productRepository, EntityManagerInterface $em, SluggerInterface $slugger, ValidatorInterface $validator)
    {
        $product = $productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException("Le produit demandé n'éxiste pas");
        }

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setSlug(strtolower($slugger->slug($product->getName())));
            $em->flush();

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'formView' => $form->createView(),
        ]);
    }

    #[Route('/admin/product/create', name: 'product_create')]
    public function create(Request $request, SluggerInterface $slugger, EntityManagerInterface $em)
    {
        $form = $this->createForm(ProductType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->getData();
            $product->setSlug(strtolower($slugger->slug($product->getName())));
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
        }

        $formView = $form->createView();

        return $this->render('product/create.html.twig', [
            'formView' => $formView
        ]);
    }
}
