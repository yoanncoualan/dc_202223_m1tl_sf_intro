<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/categories', name: 'app_category')]
    public function index(EntityManagerInterface $em, Request $request): Response
    {
        /**
         * Création du formulaire
         * Vérification et sauvegarde du formulaire
         * Récupération de la table
         */
        $category = new Category(); // Création d'un objet vide pour le formulaire
        // Appel du formulaire 'CategoryType' en lui envoyant l'objet
        $form = $this->createForm(CategoryType::class, $category);
        
        $form->handleRequest($request); // Analyse la requete HTTP
        // Si le formulaire est soumis et qu'il est valide
        if($form->isSubmitted() && $form->isValid()){
            // On sauvegarde en base la catégorie
            $em->persist($category); // Prépare la requete
            $em->flush(); // Execute la requete
        }

        $categories = $em->getRepository(Category::class)->findAll();
        /**
         * Appel de la class 'Category' grâce au 'getRepository'
         * Ensuite on lance un 'findAll' dans cette class pour récupérer toute la table
         */

        return $this->render('category/index.html.twig', [
            'categories' => $categories, // Envois de la variable $categories à la vue
            'form' => $form->createView() // Envois la version HTML du formulaire à la vue
        ]);
    }

    #[Route('/category/{id}', name: 'une_categorie')]
    public function category(Category $category = null, Request $r, EntityManagerInterface $em){
        // Symfony fait la conversion automatiquement du paramètre vers un objet (paramConverter)

        // Si la catégorie n'a pas été trouvée via le paramConverter
        if($category == null){
            $this->addFlash('danger', 'Catégorie introuvable');
            // On retourne une redirection vers la liste des catégories
            return $this->redirectToRoute('app_category');
        }

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($r);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($category);
            $em->flush();
        }

        return $this->render('category/une_categorie.html.twig', [
            'category' => $category,
            'edit' => $form->createView()
        ]);
    }

    #[Route('/category/delete/{id}', name:'category_delete')]
    public function delete(Category $category = null, EntityManagerInterface $em){
        if($category == null){
            $this->addFlash('danger', 'Catégorie introuvable');
        }
        else{
            $em->remove($category);
            $em->flush();
            $this->addFlash('warning', 'Catégorie supprimée');
        }

        return $this->redirectToRoute('app_category');
    }
}
