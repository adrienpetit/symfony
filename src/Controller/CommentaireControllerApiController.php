<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Films;
use App\Entity\Category;
use App\Entity\Comment;
use App\Repository\FilmsRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Persistence\ObjectManager;
use App\Form\FilmsType;
use App\Form\CategoryType;
use App\Form\CommentType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class CommentaireControllerApiController extends AbstractController
{
    /**
     * @Route("/commentaire/controller/api", name="commentaire_controller_api", methods={"POST", "OPTIONS"})
     */
     public function add(Request $request)
    {
        $response = new Response();
        $query = array();
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
        {
            $response->headers->set('Content-Type', 'application/text');
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, PUT, POST, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type',true);
            return $response;
        }
        $json = $request->getContent();
        $content = json_decode($json, true);
        if (isset($content["author"]) && isset($content["content"]))
        {
            $comment = new Comment();
            $comment->setTitle($content["author"]);
            $comment->setDescription($content["content"]);
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
            
            $query['valid'] = true; 
            $query['data'] = array('author' => $content["author"],
                                   'content' => $content["content"]);
            $response->setStatusCode('201');
        }
        else 
        {
            $query['valid'] = false; 
            $query['data'] = null;
            $response->setStatusCode('404');
        }        
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($query));
        return $response;
    }
}
