<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Car;
use App\Entity\User;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Length;

class Appfixtures extends Fixture
{
    private $encoder; 

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {
        $faker     = Factory::create('FR-fr');

        // Nous gérons les utilisateurs
        $users=[];
        $genres = ['male','female'];
        for ($i=1; $i<=10; $i++) {
            $user = new User;

            $genre     = $faker->randomElement($genres);
            $picture   = "https://randomuser.me/api/portraits/";
            $pictureId = $faker->numberBetween(1, 99) .'.jpg';

            if($genre == "male") {
                $picture = $picture . 'men/' . $pictureId;
            } else {
                $picture = $picture . 'women/' . $pictureId;
            }

            $user->setFirstName($faker->firstname($genre))
                 ->setLastName($faker->lastname)
                 ->setEmail($faker->email)
                 ->setAvatar($picture)
                 ->setLastName($faker->lastname)
        }

        // Nous gérons les catégory
        $categories = ['berline','4*4','suv','cabriolet'];
            foreach ($categories as $cat) {
                $category = new Category;

                $category->setName($cat); 
                $manager ->persist($category);

                // Nous gérons les voitures
                for ($j=0; $j<= mt_rand(1,5); $j++) {
                    $car = new Car();

                    $title           = $faker->sentence(2);
                    $backgroundColor = trim($faker->safeHexcolor, '#');
                    $foregroundColor = trim($faker->safeHexcolor, '#');
                    $imageCars       = "https://dummyimage.com/600x400/" . $backgroundColor . "/". $foregroundColor ."&text=" . "Voiture" ;
                    $imageP          = "https://dummyimage.com/600x400/" . $backgroundColor . "/". $foregroundColor ."&text=" . "photos appartement" ;
                    $content         = "<p>" .join("</p><p>", $faker->paragraphs(3))."</p>";
    
                    $car->setTitle($title)
                        ->setCoverImage($imageCars)
                        ->setContent($content)
                        ->setPrice(mt_rand(100, 500))
                        ->setCategory($category);

                    $manager->persist($car);
                }
            }   
        $manager->flush();
    }
}
