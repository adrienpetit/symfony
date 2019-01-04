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

class CategoryControllerApiController extends AbstractController
{
    /**
     * @Route("api/categories", name="api_categories", methods={"GET"})
     */
     //Show all categories

    public function show()
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
       
        $categories = $this->getDoctrine()
                      ->getRepository(Category::class)
                      ->findAll();
        $jsonContent = $serializer->serialize($categories, 'json');
        $response->setContent($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode('200');
        return $response;


    }
    /**
     * @Route("/api/category", name="api_category_add", methods={"POST", "OPTIONS"})
     */
    //Add category

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
        if (isset($content["title"]) && isset($content["description"]))
        {
              //new object category
            $category = new Category();
            $category->setTitle($content["title"]);
            $category->setDescription($content["description"]);
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();
            
            $query['valid'] = true; 
            $query['data'] = array('title' => $content["title"],
                                   'description' => $content["description"]);
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
     * @Route("/api/categories/edit/{id}", name="api_categories_edit", methods={"PUT", "OPTIONS"})
     */
 //Edit category
      public function editCategories(Request $request, $id)
    {
         
        $response = new Response();
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
        $encoders = array(new JsonEncoder());
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);

        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers, $encoders);
       
        $json = $request->getContent();
        $content = json_decode($json, true);
        try{
            $categories = $this->getDoctrine()
                         ->getRepository(Category::class)
                         ->find($id);
            
           
           
            
            $categories->setTitle($content["title"]);
            $categories->setDescription($content["description"]);
            $em = $this->getDoctrine()->getManager();
            $em->persist($categories);
            $em->flush();
            
            $query['valid'] = true; 
            $query['data'] = array('title' => $content["title"],
                                   'description' => $content["description"],
                                   
                                   );
            $response->setStatusCode('201');
            }
        catch(Exception $e)
        {
            $query['valid'] = false;
            $query['data'] = "Not Changed";
            $response->setStatusCode('404');
         }   
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($query));
        return $response;
        
    }
    /**
     * @Route("/api/category/{id}", name="api_category", methods={"GET"})
     */
    //Get a category with id
    public function getCategory($id)
    {
        $response = new Response();
        $encoders = array(new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        if ($id!= null) {
            $category = $this->getDoctrine()
                           ->getRepository(Category::class)
                           ->find($id);
            
            if ($category != null) {
                $jsonContent = $serializer->serialize($category, 'json');
    
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
