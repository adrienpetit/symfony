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
     * @Route("", name="film")
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
     * @Route("/film/new", name="film_add")
     */

    public function form(Request $request)
    {
        
        $films = new Films();
        
        

        
        $form = $this->createForm(FilmsType::class, $films);            

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            if(!$films->getId())
            {
                  $films->setCreateAt(new \DateTime());

            }
            try {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($films);
            $manager->flush();
            $this->addFlash('notice', "add with success");
            }
            catch (Exception $e) {
                $this->addFlash('notice', "Doesn't add with success");
            }  
                  

            return $this->redirectToRoute('film_one',['id' => $films->getId()]);

        }


        return $this->render('film/add.html.twig',array(
            'formFilms' => $form->createView()));
    }
    /**
     * @Route("/film/{id}/edit", name="film_edit")
     */
      public function editFilms(Request $request, $id)
    {
        try {
            $films = $this->getDoctrine()
                     ->getRepository(Films::class)
                     ->find($id);      
            $form = $this->createForm(FilmsType::class, $films);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid())
            {
                try {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($films);
                    $em->flush();
                    $this->addFlash('notice', "Modify with success");
                }
                catch (Exception $e) {
                    $this->addFlash('notice', "Doesn't modify with success");
                }  
                     
                return $this->redirect($this->generateUrl('film'));         
            }
        }
        catch (Exception $e) {
            $this->addFlash('notice', "Error");
            return $this->redirect($this->generateUrl('film'));
        }
        return $this->render('film/edit.html.twig', array('form' => $form->createView()));
    }
    /**
     * @Route("/film/{id}/del", name="film_del")
     */

    public function delFilms($id=null){

       try {
            $films = $this->getDoctrine()
                      ->getRepository(Films::class)
                      ->findAll();
            if ($id != null) {
                try {
                    $em = $this->getdoctrine()->getManager();
                    $films = $em->getRepository(Films::class)->find($id);
                    $em->remove($films);
                    $em->flush();
                    $this->addFlash('notice', "Delete with success");
                }
                catch (Exception $e) {
                    $this->addFlash('notice', "Doesn't delete with success");
                }
                
            }
        }
        catch (Exception $e) {
            $this->addFlash('notice', "Error");
        }        
        return $this->redirect($this->generateUrl('film'));
    
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
