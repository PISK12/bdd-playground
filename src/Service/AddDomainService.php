<?php

namespace App\Service;

use App\Entity\Customer;
use App\Entity\Domain;
use App\Repository\DomainRepository;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;

final class AddDomainService
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly DomainRepository $domainRepository)
    {
    }

    public function doIt(Customer $customer, string $name): int
    {
        if ($this->domainRepository->findOneBy(['name' => $name])) {
            throw new RuntimeException();
        }

        $explodeName = explode('.', $name);
        if (count($explodeName) > 2) {
            $nameRootDomain = implode('.', array_slice($explodeName, 1));
            /** @var ?Domain $rootDomain */
            $rootDomain = $this->domainRepository->findOneBy(['name' => $nameRootDomain]);
            if ($rootDomain !== null && !$rootDomain->isPublic() && $rootDomain->getOwner() !== $customer) {
                throw new \RuntimeException('Someone is owner this domain');
            }
        }

        $domain = new Domain($name,$customer,false);

        $this->entityManager->persist($domain);
        $this->entityManager->flush();

        return $domain->getId();
    }
}