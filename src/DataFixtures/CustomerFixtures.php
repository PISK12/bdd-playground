<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CustomerFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($index=0;$index<10;$index++){
            $customer = (new Customer())
                ->setName($faker->name);
            $manager->persist($customer);
        }

        $manager->flush();
    }
}
