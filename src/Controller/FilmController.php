<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Films;
use App\Repository\FilmsRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use App\Form\FilmsType;


class FilmController extends AbstractController
{
    /**
     * @Route("/film", name="film")
     */
    public function index(FilmsRepository $repo)
    {
        
        $films = $repo->findAll();
        return $this->render('film/index.html.twig', [
            'controller_name' => 'FilmController',
            'films' => $films
        ]);
    }
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
    	return $this->render ('film/home.html.twig', ['title' => "Bienvenu !",
            'age'=> 23
        ]);
    }

    /**
     * @Route("/film/new", name="film_add")
     * @Route("/film/{id}/edit", name="film_edit")
     */

    public function form(Films $films = null,Request $request,  ObjectManager $manager)
    {
        if(!$films)
        {
            $films = new Films();
        }
        

        
        $form = $this->createForm(FilmsType::class, $films);            

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            if(!$films->getId())
            {
                  $films->setCreateAt(new \DateTime());

            }
          
            $manager->persist($films);
            $manager->flush();

            return $this->redirectToRoute('film_one',['id' => $films->getId()]);

        }


        return $this->render('film/add.html.twig',[
            'formFilms' => $form->createView(),
            'editMode' => $films->getId() !== null
        ]);
    }

    /**
     * @Route("/film/{id}", name="film_one")
     */

    public function one(Films $films){

        return $this->render('film/one.html.twig',[
            'films' => $films
        ]);
    }
    
}
