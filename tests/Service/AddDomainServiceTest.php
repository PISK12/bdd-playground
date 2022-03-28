<?php

namespace App\Tests\Service;

use App\Entity\Customer;
use App\Repository\DomainRepository;
use App\Service\AddDomainService;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @property Connection $connection
 * @property EntityManagerInterface $em
 * @property AddDomainService $service
 * @property DomainRepository $domainRepository
 */
class AddDomainServiceTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::$container;

        $this->connection = $container->get(Connection::class);
        $this->em = $container->get(EntityManagerInterface::class);
        $this->service = $container->get(AddDomainService::class);
        $this->domainRepository = $container->get(DomainRepository::class);
    }

    public function testDoIt(): void
    {
        $domainId = $this->service->doIt($this->getCustomer('Customer001'),'tested.com');
        $this->assertIsNumeric($domainId);
        $domain = $this->domainRepository->findOneBy(['name'=>'tested.com']);
        $this->assertEquals($domainId,$domain->getId());
        $this->assertEquals('tested.com',$domain->name());
    }

    public function testDoItTwice(): void
    {
        $customer = $this->getCustomer('Customer001');
        $this->service->doIt($customer,'tested.com');

        $this->expectException(\RuntimeException::class);
        $this->service->doIt($customer,'tested.com');
    }

    public function testDoItForSubdomainForTheSameCustomer(): void
    {
        $customer = $this->getCustomer('Customer001');
        $this->service->doIt($customer,'tested.com');

        $subDomainId = $this->service->doIt($customer,'test.tested.com');
        $this->assertIsNumeric($subDomainId);
    }

    private function getCustomer(string $name):Customer{
        $customer = (new Customer())
            ->setName($name)
        ;

        $this->em->persist($customer);
        $this->em->flush();
        return $customer;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    protected function tearDown(): void
    {
        $this->connection->executeQuery('DELETE FROM domain;');
        $this->connection->executeQuery('DELETE FROM customer;');
        $this->connection->executeQuery('DELETE FROM page;');
    }
}
