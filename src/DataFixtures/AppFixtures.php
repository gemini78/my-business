<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    
    public function __construct(readonly SluggerInterface $slugger) {}
    
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($c=0; $c < 3; $c++) { 
            $category = new Category();
            $category->setName($faker->words(mt_rand(2, 4), true))
            ->setSlug(strtolower($this->slugger->slug($category->getName())));

            $manager->persist($category);

            for ($p=0; $p < mt_rand(15, 20); $p++) { 
                $product = new Product();
                $product
                    ->setName($faker->words(mt_rand(2, 4), true))
                    ->setPrice(mt_rand(4000, 20000))
                    ->setSlug(strtolower($this->slugger->slug($product->getName())))
                    ->setCategory($category)
                    ->setShortDescription($faker->paragraph())
                    ->setMainPicture('https://picsum.photos/400/400?image=' . mt_rand(100, 700));
                    $manager->persist($product);
            }
        }

        
        $manager->flush();
    }
}
