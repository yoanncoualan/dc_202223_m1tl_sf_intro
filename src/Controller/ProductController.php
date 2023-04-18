<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/product')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product')]
    public function index(EntityManagerInterface $em, Request $r): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($r);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($product);
            $em->flush();
            $this->addFlash('success', 'Produit ajouté');
        }

        $products = $em->getRepository(Product::class)->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'add' => $form->createView()
        ]);
    }

    #[Route('/{id}', name:'un_produit')]
    public function show(Product $product = null, Request $r, EntityManagerInterface $em){
        if($product == null){
            $this->addFlash('danger', 'Produit introuvable');
            return $this->redirectToRoute('app_product');
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($r);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($product);
            $em->flush();
            $this->addFlash('success', 'Produit modifié');
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'edit' => $form->createView()
        ]);
    }

    #[Route('/delete/{id}', name:'delete_product')]
    public function delete(Product $product = null, EntityManagerInterface $em){
        if($product == null){
            $this->addFlash('danger', 'Produit introuvable');
        }
        else{
            $em->remove($product);
            $em->flush();
            $this->addFlash('warning', 'Produit supprimé');
        }
        return $this->redirectToRoute('app_product');
    }
}
