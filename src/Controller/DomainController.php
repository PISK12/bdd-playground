<?php

namespace App\Controller;

use App\Repository\CustomerRepository;
use App\Repository\DomainRepository;
use App\Service\AddDomain;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class DomainController extends AbstractController
{
    public function __construct(
        private readonly DomainRepository $domainRepository,
        private readonly CustomerRepository $customerRepository,
        private readonly AddDomain $addDomain,
    )
    {
    }

    /**
     * @Route("/domain", name="app_add_domain", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {
        $customerId = $request->get('customerId');
        $customer = $this->customerRepository->find($customerId);
        if ($customer === null) {
            throw new NotFoundHttpException();
        }

        $domain = $request->get('domain');

        $domainId = $this->addDomain->__invoke($customer, $domain);
        return $this->json(['id' => $domainId], Response::HTTP_CREATED);
    }


    /**
     * @Route("/customer/{customerId}/domain", name="app_list_domain_for_customer", methods={"GET"})
     */
    public function ofCustomer($customerId): JsonResponse
    {
        $customer = $this->customerRepository->find($customerId);
        if ($customer === null) {
            throw new NotFoundHttpException();
        }

        return $this->json($this->domainRepository->findBy(['owner' => $customer]));
    }

    /**
     * @Route("/domain", name="app_list_domain", methods={"GET"})
     */
    public function list(): JsonResponse
    {
        return $this->json($this->domainRepository->findAll());
    }

    /**
     * @Route("/domain/{domainId}/asPublic", name="app_as_public_domain", methods={"POST"})
     */
    public function asPublic(int $domainId): JsonResponse
    {
        $domain = $this->domainRepository->find($domainId);
        if ($domain === null) {
            throw new NotFoundHttpException();
        }
        if ($domain->isPublic()) {
            throw new InvalidArgumentException();
        }
        $domain->setIsPublic(true);
        return new JsonResponse([]);
    }

    /**
     * @Route("/domain/{domainId}/asPrivate", name="app_as_private_domain", methods={"POST"})
     */
    public function asPrivate(int $domainId): JsonResponse
    {
        $domain = $this->domainRepository->find($domainId);
        if ($domain === null) {
            throw new NotFoundHttpException();
        }
        if (!$domain->isPublic()) {
            throw new InvalidArgumentException();
        }

        $domain->setIsPublic(false);
        return new JsonResponse();
    }
}
