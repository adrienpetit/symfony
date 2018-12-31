<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Films;
use App\Entity\Comment;
use App\Form\CommentType;


class CommentaireController extends AbstractController
{
    /**
     * @Route("/film/{id}/commentaire", name="commentaire")
     */
     public function add(Request $request, $id)
    {   
        try {
            $films = $this->getDoctrine()
                     ->getRepository(Films::class)
                     ->find($id);
        $comment = new Comment();
        
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $comment->setCreatedAt(new \DateTime());
            $comment->setFilm($films);
            try{
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($comment);
                $manager->flush();
    
                $this->addFlash('notice', 'Comment add');
                return $this->redirectToRoute('film');
            } catch (Exception $e) {
                $this->addFlash('notice', " Doesn't add with success");
            }            

        }
    }
    catch (Exception $e) {
            $this->addFlash('notice', "Error");
            return $this->redirect($this->generateUrl('film'));
        }
    return $this->render('film/commentaire.html.twig', [
        'formComment' => $form->createView(),
        
    ]);
    }
     /**
     * @Route("/comment/{id}/del", name="com_del")
     */

    public function delCom($id=null){

       try {
            $comment = $this->getDoctrine()
                      ->getRepository(Comment::class)
                      ->findAll();
            if ($id != null) {
                try {
                    $em = $this->getdoctrine()->getManager();
                    $comment = $em->getRepository(Comment::class)->find($id);
                    $em->remove($comment);
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


}
