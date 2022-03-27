<?php

namespace App\Service;

use App\Entity\Customer;
use App\Entity\Domain;
use App\Repository\DomainRepository;
use Doctrine\ORM\EntityManagerInterface;
use http\Exception\InvalidArgumentException;
use Pdp\Rules;
use Pdp\Domain as PdpDomain;

final class AddDomain
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly DomainRepository $domainRepository)
    {
    }

    public function __invoke(Customer $customer, string $name): int
    {
        if ($this->domainRepository->findBy(['name' => $name])) {
            throw new InvalidArgumentException();
        }

        $explodeName = explode('.', $name);
        if (count($explodeName) > 2) {
            $nameRootDomain = implode('.', array_slice($explodeName, 1));
            /** @var ?Domain $rootDomain */
            $rootDomain = $this->domainRepository->findBy(['name' => $nameRootDomain]);
            if ($rootDomain !== null && !$rootDomain->isPublic() && $rootDomain->getOwner() !== $customer) {
                throw new \InvalidArgumentException('Someone is owner this domain');
            }
        }


        $domain = (new Domain())
            ->setName($name)
            ->setIsPublic(false)
            ->setOwner($customer);

        $this->entityManager->persist($domain);
        $this->entityManager->flush();

        return $domain->getId();
    }
}