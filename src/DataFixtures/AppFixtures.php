<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Entity\User;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{

    public function __construct(readonly SluggerInterface $slugger, readonly UserPasswordHasherInterface $encoder)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $users = [];

        $admin = new User();

        $hash = $this->encoder->hashPassword($admin, "password");
        $admin
            ->setEmail("admin@gmail.com")
            ->setFullName("Admin")
            ->setPassword($hash)
            ->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        for ($u = 0; $u < 5; $u++) {
            $user = new User();
            $hash = $this->encoder->hashPassword($user, "password");
            $user
                ->setEmail("user$u@gmail.com")
                ->setFullName($faker->name())
                ->setPassword($hash);
            $users[] = $user;
            $manager->persist($user);
        }

        $users[] = $admin;
        $products = [];

        for ($c = 0; $c < 3; $c++) {
            $category = new Category();

            $category
                ->setName($faker->words(mt_rand(2, 4), true))
                ->setSlug(strtolower($this->slugger->slug($category->getName())))
                ->setOwner($faker->randomElement($users));

            $manager->persist($category);

            for ($p = 0; $p < mt_rand(15, 20); $p++) {
                $product = new Product();
                $product
                    ->setName($faker->words(mt_rand(2, 4), true))
                    ->setPrice(mt_rand(4000, 20000))
                    // ->setSlug(strtolower($this->slugger->slug($product->getName())))
                    ->setCategory($category)
                    ->setShortDescription($faker->paragraph())
                    ->setMainPicture('https://picsum.photos/400/400?image=' . mt_rand(100, 700));

                $products[] = $product;

                $manager->persist($product);
            }
        }

        for ($p = 0; $p < mt_rand(20, 40); $p++) {
            $purchase = new Purchase();
            $purchase
                ->setFullName($faker->words(mt_rand(2, 4), true))
                ->setAddress($faker->streetAddress)
                ->setPostalCode($faker->postcode)
                ->setCity($faker->city)
                ->setUser($faker->randomElement($users))
                ->setTotal(mt_rand(2000, 30000))
                ->setPurchaseAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6months')));

            $selectProducts = $faker->randomElements($products, mt_rand(3, 5));

            foreach ($selectProducts as $product) {
                $purchaseItem = new PurchaseItem();
                $purchaseItem
                    ->setProduct($product)
                    ->setQuantity(mt_rand(3, 5))
                    ->setProductName($product->getName())
                    ->setProductPrice($product->getPrice())
                    ->setTotal(
                        $purchaseItem->getProductPrice() * $purchaseItem->getQuantity()
                    )
                    ->setPurchase($purchase);
                $manager->persist($purchaseItem);
            }

            if ($faker->boolean(90)) {
                $purchase->setStatus(Purchase::STATUS_PAID);
            }
            $manager->persist($purchase);
        }
        $manager->flush();
    }
}
