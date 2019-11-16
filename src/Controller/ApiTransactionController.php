<?php

namespace App\Controller;

use App\Entity\CustomTransactionType;
use App\Entity\Transaction;
use App\Entity\TransactionType;
use App\Repository\CustomTransactionTypeRepository;
use App\Repository\TransactionRepository;
use App\Repository\TransactionTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ApiTransactionController
 * @package App\Controller
 * @IsGranted("ROLE_USER")
 */
class ApiTransactionController extends AbstractController
{
    /**
     * @Route("/api/transaction/type/{type}", name="api_transaction_type", methods={"GET"})
     */
    public function getTransactions($type, CustomTransactionTypeRepository $customTransactionTypeRepository)
    {
        $list = $customTransactionTypeRepository->findTransactionList($type, $this->getUser());
        return $this->json($list, 200, [], ['groups' => 'customTransactionList']);
    }

    /**
     * @param Transaction $transaction
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/transaction/edit/{id}", name="api_transaction_edit", methods={"PUT"})
     */
    public function updateTransaction(Transaction $transaction, EntityManagerInterface $em, Request $request)
    {
        if(!$transaction) {
            return $this->json(['error' => 1], Response::HTTP_BAD_REQUEST);
        }

        $transaction->setAmount($request->request->get('amount'));
        $em->flush();
        return $this->json(['success' => 1], Response::HTTP_OK);
    }

    /**
     * @param Transaction $transaction
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/transaction/{id}", name="api_get_one_transaction", methods={"GET"})
     */
    public function getOneTransaction(Transaction $transaction)
    {
        return $this->json($transaction, 200, [], ['groups' => 'one_transaction_read']);
    }

    /**
     * @param Transaction $transaction
     * @Route("/api/transaction/{id}", name="api_transaction_delete", methods={"DELETE"})
     */
    public function removeTransaction(Transaction $transaction, EntityManagerInterface $em)
    {
        if($transaction->getUser()->getId() != $this->getUser()->getId()) {
            return $this->json(['error' => 1], Response::HTTP_BAD_REQUEST);
        }

        $em->remove($transaction);
        $em->flush();
        return $this->json(['success' => 1], Response::HTTP_OK);
    }

    /**
     * @param $transactionId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/api/transaction/name/{transactionId}", name="api_transaction_name_delete", methods={"GET"})
     */
    public function removeTransactionsName($transactionId, CustomTransactionTypeRepository $customTransactionTypeRepository, TransactionTypeRepository $transactionTypeRepository, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        $transactionType = $transactionTypeRepository->findOneBy(['id' => $transactionId]);
        $customTransactionType = $customTransactionTypeRepository->findOneBy(['user' => $user, 'transactionType' => $transactionType]);
        if(!$transactionType or !$customTransactionType) {
            return $this->json([], Response::HTTP_BAD_REQUEST);
        }

        $em->remove($customTransactionType);
        $em->flush();
        return $this->json(['success' => 1], Response::HTTP_OK);
    }

    /**
     * @Route("/api/transaction/add", name="api_transaction_add", methods={"POST"})
     */
    public function addTransaction(Request $request, EntityManagerInterface $em)
    {
        $transaction = new Transaction();

        $transaction->setAmount(str_replace (',','',$request->request->get('balance')));
        $transactionType = $em->find(TransactionType::class, $request->request->get('transactionName'));
        $transaction->setUser($this->getUser());
        $transaction->setTransactionType($transactionType);
        $em->persist($transaction);
        $em->flush();

        return $this->json(true);
    }

    /**
     * @Route("/api/customTransaction/add", name="api_add_custom_transaction", methods={"POST"})
     */
    public function addCustomTransactionType(   Request $request,
                                                EntityManagerInterface $em,
                                                TransactionTypeRepository $transactionTypeRepository,
                                                CustomTransactionTypeRepository $customRepository)
    {
        $type = strtolower($request->request->get('type'));
        $name = ucfirst(strtolower($request->request->get('name')));

        if($type == 'income' or $type == 'outcome') {
            if($transaction = $transactionTypeRepository->findOneBy(['name' => $name])) {
                if($customTransaction = $customRepository->findOneBy(['transactionType' => $transaction, 'user' => $this->getUser()])) {
                    return $this->json(['error' => 1], Response::HTTP_BAD_REQUEST);
                }

                $customT = new CustomTransactionType();
                $customT->setUser($this->getUser());
                $customT->setTransactionType($transaction);
                $em->persist($customT);
                $em->flush();

                return $this->json(true);
            }

            $transactionType = new TransactionType();
            $transactionType->setName($name);
            $transactionType->setType($type);

            $em->persist($transactionType);
            $em->flush();

            return $this->json(true, Response::HTTP_OK);
        }

        return $this->json(false, Response::HTTP_BAD_REQUEST);
    }
}
