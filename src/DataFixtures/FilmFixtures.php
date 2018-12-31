<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Films;
use App\Entity\Category;
use App\Entity\Comment;
class FilmFixtures extends Fixture
{
    //Fonction pour ajouter de fausses données a la bdd
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');
        // créer 3 catégories 
            for ($i=1; $i <=3 ; $i++) 
            { 
                $category = new Category();
                $category->setTitle($faker->sentence())
                        ->setDescription($faker->paragraph());
                $manager->persist($category);
                
                //crée 4-6actricles
                    for($j = 1; $j <=mt_rand(4,6); $j++)
                {
                    $film = new Films();

                    
                    $content ='<p>'.join($faker->paragraphs(5), '</p><p>').'</p>';
                    


                    $film->setTitle($faker->sentence())
                        ->setContent ($content)
                        ->setImage($faker->imageUrl())
                        ->setCreateAt($faker->dateTimeBetween('-6 months'))
                        ->setCategory($category);

                    $manager->persist($film); 

                    //commentaire à un artcile
                        for ($k=1; $k <= mt_rand(4,10) ; $k++) { 

                            $comment = new Comment();
                            $content ='<p>';
                            $content .=join($faker->paragraphs(2), '</p><p>');
                            $content .= '</p>';

                          
                            $days = (new \DateTime())->diff($film->getCreateAt())->days;
                            
                            

                            $comment->setAuthor($faker->name)
                                    ->setContent($content)
                                    ->setCreatedAt($faker->dateTimeBetween('-'.$days.'days'))
                                    ->setFilm($film);

                            $manager->persist($comment);        
                              
                          }  
                }
            
            }
        
        $manager->flush();
    }
}

