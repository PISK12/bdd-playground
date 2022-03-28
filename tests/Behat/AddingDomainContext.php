<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Entity\Customer;
use App\Entity\Domain;
use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\Profiler\Profiler;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class AddingDomainContext implements Context
{
    private array $customers = [];
    private array $domains = [];
    private \Symfony\Component\HttpFoundation\Response $response;

    /**
     * @BeforeScenario
     */
    public function cleanDatabase()
    {
        $this->connection->executeQuery('DELETE FROM domain;');
        $this->connection->executeQuery('DELETE FROM customer;');
        $this->connection->executeQuery('DELETE FROM page;');
    }

    public function __construct(
        private readonly Connection $connection,
        private readonly EntityManagerInterface $entityManager,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly KernelInterface $kernel,
    )
    {
    }

    /**
     * @Then the response should be received
     */
    public function theResponseShouldBeReceived(): void
    {
        if ($this->response === null) {
            throw new \RuntimeException('No response received');
        }
        $data = json_decode($this->response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        if (empty($data['id'])) {
            throw new \RuntimeException('No response received');
        }
    }

    /**
     * @Given /^the customer "([^"]*)"$/
     */
    public function theCustomer(string $name)
    {
        $customer = (new Customer())
            ->setName($name);
        $this->entityManager->persist($customer);
        $this->entityManager->flush();
        $this->customers[$customer->getName()] = $customer;
    }

    /**
     * @When /^adding domain "([^"]*)" by "([^"]*)"$/
     * @throws \JsonException
     */
    public function addingDomain(string $domainName, string $customerName)
    {
        $this->response = $this->kernel->handle(
            Request::create(
                uri: $this->urlGenerator->generate('app_add_domain', []),
                method: 'POST',
                server: [
                    'CONTENT_TYPE' => 'application/json'
                ],
                content: json_encode([
                    'customerId' => $this->customers[$customerName]->getId(),
                    'domain' => $domainName
                ], JSON_THROW_ON_ERROR)),
            HttpKernelInterface::SUB_REQUEST,
        );

    }

    /**
     * @Then the response status code should be :arg1
     */
    public function theResponseStatusCodeShouldBe($code)
    {
        if ($this->response->getStatusCode() !== (int)$code) {
            throw new \RuntimeException("Expected code '{$code}' not received.\nReceived '{$this->response->getStatusCode()}'.\nMessage '{$this->response->getContent()}'");
        }
    }

    /**
     * @Given /^the domain "([^"]*)" by "([^"]*)"$/
     */
    public function theDomainByCustomer(string $domainName, string $customerName)
    {
        $domain = new Domain($domainName, $this->customers[$customerName], false);
        $this->entityManager->persist($domain);
        $this->entityManager->flush();
        $this->domains[] = $domain;
    }

    /**
     * @Given /^the public domain "([^"]*)" by "([^"]*)"$/
     */
    public function thePublicDomainByCustomer(string $domainName, string $customerName)
    {
        $domain = new Domain($domainName, $this->customers[$customerName], true);
        $this->entityManager->persist($domain);
        $this->entityManager->flush();
        $this->domains[] = $domain;
    }

}
