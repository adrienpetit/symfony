<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use App\Entity\Category;
use App\Form\CategoryType;


class CategoryController extends AbstractController
{
    /**
     * @Route("/categorie/category", name="category_add")
     */
     public function add(Request $request, ObjectManager $manager)
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            try{
                $manager->persist($category);
                $manager->flush();
    
                $this->addFlash('message', 'Catégorie ajoutée');
                return $this->redirectToRoute('film');
            } catch (Exception $e) {
                $this->addFlash('message', "La catégorie n'a pas été ajouté");
            }            

        }

        return $this->render('categorie/category.html.twig', [
            'formCategory' => $form->createView()
        ]);
    }
}
