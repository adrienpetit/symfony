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

class FilmControllerApiController extends AbstractController
{
    /**
     * @Route("/api/index", name="api_index",methods={"GET"})
     */
    public function index()
    {
    	$response = new Response();
        $query = array();
        $encoders = array(new JsonEncoder());
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);

        $normalizer->setCircularReferenceHandler(function ($object) {
		    return $object->getId();
		});
		$normalizers = array($normalizer);
        $serializer = new Serializer($normalizers, $encoders);
        $films = $this->getDoctrine()
                      ->getRepository(Films::class)
                      ->findAll();
        $jsonContent = $serializer->serialize($films, 'json');
        $response->setContent($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode('200');
        return $response;


    }
    
  	/**
     * @Route("/api/form", name="api_form", methods={"POST", "OPTIONS"})
     */
    public function form(Request $request)
    {
       	$response = new Response();
        $query = array();
        $encoders = array(new JsonEncoder());
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);

        $normalizer->setCircularReferenceHandler(function ($object) {
		    return $object->getId();
		});
		$normalizers = array($normalizer);
        $serializer = new Serializer($normalizers, $encoders);
        $query = array();
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
        {
            $response = new Response();
            $response->headers->set('Content-Type', 'application/text');
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, PUT, POST, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type',true);
            return $response;
        }
        $json = $request->getContent();
        $content = json_decode($json, true);
        if (isset($content["title"]) && isset($content["content"]) && isset($content["image"]))
        {
            $films = new Films();
            $category = $this->getDoctrine()
                             ->getRepository(Category::class)
                             ->find($content["category"]);
           
            $films->setTitle($content["title"]);
            $films->setContent($content["content"]);
            $films->setImage($content["image"]);
            $films->setCategory($category);
           // $films->setFkState($state);
            $em = $this->getDoctrine()->getManager();
            $em->persist($films);
            $em->flush();            
            
            $query['valid'] = true; 
            $query['data'] = array('title' => $content["title"],
                                   'content' => $content["description"],
                                   'category' => json_decode($serializer->serialize($category, 'json')),
                                   );
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

    /**
     * @Route("/api/film/{id}/edit", name="api_film_edit", methods={"POST", "OPTIONS"})
     */
      public function editFilms(Request $request, $id)
    {
    	$response = new Response();
        $query = array();
        $encoders = array(new JsonEncoder());
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);

        $normalizer->setCircularReferenceHandler(function ($object) {
		    return $object->getId();
		});
		$normalizers = array($normalizer);
        $serializer = new Serializer($normalizers, $encoders);
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
        {
            $response = new Response();
            $response->headers->set('Content-Type', 'application/text');
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, PUT, POST, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type',true);
            return $response;
        }
        $json = $request->getContent();
        $content = json_decode($json, true);
        if ($id!= null && isset($content["title"]) && isset($content["content"]) && isset($content["image"]))
        {
            $films = $this->getDoctrine()
                         ->getRepository(Films::class)
                         ->find($id);
            
            $category = $this->getDoctrine()
                             ->getRepository(Category::class)
                             ->find($content["category"]);
           
            
            $films->setTitle($content["title"]);
            $films->setContent($content["content"]);
            $films->setImage($content["image"]);
            $films->setCategory($category);
            $em = $this->getDoctrine()->getManager();
            $em->persist($films);
            $em->flush();
            
            $query['valid'] = true; 
            $query['data'] = array('title' => $content["title"],
                                   'content' => $content["description"],
                                   'category' => json_decode($serializer->serialize($category, 'json')),
                                   );
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
    /**
     * @Route("/api/film/{id}/del", name="api_film_del", methods={"DELETE", "OPTIONS"})
     */

    public function delFilms($id=null){

       $response = new Response();
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
        {
            $response = new Response();
            $response->headers->set('Content-Type', 'application/text');
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, PUT, POST, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type',true);
            return $response;
        }
        if ($id != null) {
            $em = $this->getdoctrine()->getManager();
            $films = $em->getRepository(Films::class)->find($id);
            $em->remove($films);
            $em->flush();
            $query['valid'] = true;
            $response->setStatusCode('200');
        }
        else
        {
            $query['valid'] = false;
            $response->setStatusCode('404');
        }
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($query));
        return $response;
    
    
    }

    /**
     * @Route("/api/film/{id}", name="api_film_one", methods={"GET"})
     */

    public function one($id){

        $response = new Response();
        $query = array();
        $encoders = array(new JsonEncoder());
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);

        $normalizer->setCircularReferenceHandler(function ($object) {
		    return $object->getId();
		});
       	$normalizers = array($normalizer);
        $serializer = new Serializer($normalizers, $encoders);
        if ($id != null) {
            $films = $this->getDoctrine()
                      ->getRepository(Films::class)
                      ->find($id);
            
            if ($films != null) {
                $jsonContent = $serializer->serialize($films, 'json');
    
                $response->setContent($jsonContent);
                $response->headers->set('Content-Type', 'application/json');
                $response->setStatusCode('200');
            }
            else {
                $response->setStatusCode('404');
            }
        }        
        else {
            $response->setStatusCode('404');
        }
        return $response;
    }
    	
    
}
