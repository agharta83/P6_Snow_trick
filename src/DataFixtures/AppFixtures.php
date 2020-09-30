<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use App\Entity\Images;
use App\Entity\Tricks;
use App\Entity\Users;
use App\Tools\FormatedText;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        // 4 Users
        $users = $this->createUsersFixtures($faker, $manager, 4);

        // All Catégories
        $categories = $this->createCategoriesFixtures($manager);

        // 6 Tricks with images
        $this->createTricksFixtures($manager, $faker, $users, $categories);

        $manager->flush();
    }

    private function createUsersFixtures(Faker\Generator $faker, ObjectManager $manager, int $nbOfUsersToCreate)
    {
        $users = array();
        for ($i = 0; $i < $nbOfUsersToCreate; $i++) {
            $user = new Users();
            $user->setEmail($faker->safeEmail);
            $user->setCreatedAt($faker->dateTimeBetween('-6 months'));
            $user->setPassword($this->encoder->encodePassword($user, $faker->password));
            $user->setUsername($faker->userName);
            $user->setIsVerified(true);

            $manager->persist($user);
            $users[$i] = $user;
        }

        return $users;
    }

    private function createCategoriesFixtures(ObjectManager $manager)
    {
        $categoriesName = ['Grabs', 'Rotations', 'Flips', 'Rotations désaxées', 'Slides', 'One foot', 'Old school'];
        $categories = array();

        foreach ($categoriesName as $categoryName) {
            $category = new Categories();
            $category->setName($categoryName);
            $category->setSlug(FormatedText::slugify($categoryName));

            $manager->persist($category);

            $categories[] = $category;
        }

        return $categories;
    }

    private function createTricksFixtures(ObjectManager $manager, Faker\Generator $faker, $users, $categories)
    {
        $tricksName = ['Tail grab', 'Indy', '360', '180', 'Front flip', 'Rodeo'];

        foreach ($tricksName as $trickName) {
            for ($k = 0; $k < 3; $k++) {
                $trick = new Tricks();
                $trick->setName($trickName);
                $trick->setDescription($faker->paragraph(8, true));
                $trick->setCreated_at($faker->dateTimeBetween('-6 months'));
                $trick->setSlug(FormatedText::slugify($trickName));
                $trick->setUser($faker->randomElement($users));
                $trick->setCategory(($faker->randomElement($categories)));

                for ($i = 1; $i < 4; $i++) {
                    $image = new Images();
                    $image->setName($trick->getName() . $i . '.jpg');
                    $image->setTricks($trick);

                    $manager->persist($image);

                    if ($i === 3) {
                        $trick->setMain_image($image);
                        $manager->persist($trick);
                    }
                }
            }

        }
    }
}
