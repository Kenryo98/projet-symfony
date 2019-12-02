<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Tests\Fixtures\Validation\Article;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 1; $i <= 20; $i++) {
            $product = new Product();
    
            $sentence = $faker->sentence(4);
            $name = substr($sentence, 0, strlen($sentence) - 1);
            $product->setName($name)
                    ->setPrice($faker->randomNumber(2))
                    ->setDescription($faker->text(3000))
                    ->setCreatedAt($faker->dateTimeThisYear());
    
            $manager->persist($product);
        }
    
        $manager->flush();
    }
}