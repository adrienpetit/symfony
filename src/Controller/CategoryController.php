<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Repository\CategoryRepository;
use App\Entity\Category;
use App\Form\CategoryType;


class CategoryController extends AbstractController
{

    /**
     * @Route("/categories", name="categories")
     */
        //Fonction pour montrer les catégories

    public function show(CategoryRepository $repo)
    {
        
        $categories = $repo->findAll();
        return $this->render('categorie/categories.html.twig', [
            'controller_name' => 'CategoryController',
            'categories' => $categories
        ]);
    }
    /**
     * @Route("/categorie/category", name="category_add")
     */
        //Fonction pour ajouter une catégorie

     public function add(Request $request, ObjectManager $manager)
    {
        //nouvel objet catégorie
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        //vérification du formulaire envoyé
        if($form->isSubmitted() && $form->isValid())
        {

            try{
                $manager->persist($category);
                $manager->flush();
    
                $this->addFlash('notice', 'Catégorie ajoutée');
                return $this->redirectToRoute('film');
            } catch (Exception $e) {
                $this->addFlash('notice', "La catégorie n'a pas été ajouté");
            }            

        }

        return $this->render('categorie/category.html.twig', [
            'formCategory' => $form->createView()
        ]);
    }
    /**
     * @Route("/categorie/category/{id}/edit", name="category_edit")
     */
        //Fonction pour modifier un commentaire

      public function editCategory(Request $request, $id)
    {
        try {
            $category = $this->getDoctrine()
                     ->getRepository(Category::class)
                     ->find($id);      
            $form = $this->createForm(CategoryType::class, $category);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid())
            {
                try {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($category);
                    $em->flush();
                    $this->addFlash('notice', "Modify with success");
                }
                catch (Exception $e) {
                    $this->addFlash('notice', "Doesn't modify with success");
                }  
                     
                return $this->redirect($this->generateUrl('categories'));         
            }
        }
        catch (Exception $e) {
            $this->addFlash('notice', "Error");
            return $this->redirect($this->generateUrl('categories'));
        }
        return $this->render('categorie/editCategory.html.twig', array('form' => $form->createView()));
    }
}
