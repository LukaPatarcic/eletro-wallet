<?php

namespace App\Controller;

use App\Repository\TransactionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserDashboardController
 * @package App\Controller
 * @IsGranted("ROLE_USER")
 */
class UserDashboardController extends AbstractController
{

    /**
     * @Route("/user/dashboard", name="app_dashboard")
     */
    public function index(TransactionRepository $repository)
    {
        $user = $this->getUser();
        $transactions = $repository->findLastFiveTransactions($user->getEmail());

        return $this->render('user_dashboard/index.html.twig',['transactions' => $transactions,]);
    }

    /**
     * @Route("/user/dashboard/profile", name="app_dashboard_profile")
     */
    public function profile()
    {
        return $this->render('user_dashboard/profile.html.twig');
    }

    /**
     * @Route("/user/dashboard/statistics", name="app_dashboard_statistics")
     */
    public function statistics()
    {
        return $this->render('user_dashboard/statistics.html.twig');
    }

    /**
     * @Route("/user/dashboard/tables", name="app_dashboard_tables")
     */
    public function tables()
    {
        return $this->render('user_dashboard/tables.html.twig');
    }

    /**
     * @Route("/user/dashboard/contact", name="app_dashboard_contact")
     */
    public function contact()
    {
        return $this->render('user_dashboard/contact.html.twig');
    }
}
