<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use App\Entity\Comment;
use App\Form\CommentType;


class CommentaireController extends AbstractController
{
      /**
     * @Route("/film/commentaire", name="commentaire")
     */
     public function add(Request $request, ObjectManager $manager)
    {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            try{
                $manager->persist($comment);
                $manager->flush();
    
                $this->addFlash('message', 'Commentaire ajoutée');
                return $this->redirectToRoute('film');
            } catch (Exception $e) {
                $this->addFlash('message', "Le commentaire n'a pas été ajouté");
            }            

        }

        return $this->render('film/commentaire.html.twig', [
            'formComment' => $form->createView(),
            
        ]);
    }

}
