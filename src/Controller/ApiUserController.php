<?php

namespace App\Controller;

use App\Repository\TransactionRepository;
use App\Services\ChartDataMaker;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ApiUserController
 * @package App\Controller
 * @IsGranted("ROLE_USER")
 */
class ApiUserController extends AbstractController
{

    /**
     * @var ChartDataMaker
     */
    private $dataMaker;


    public function __construct(ChartDataMaker $dataMaker)
    {
        $this->dataMaker = $dataMaker;
    }

    /**
     * @Route("/api/user", name="api_user_balance", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function userBalance()
    {
        return $this->json($this->getUser(), Response::HTTP_OK, [], ['groups' => 'info']);
    }

    /**
     * @param TransactionRepository $repository
     * @param Request $request
     * @Route("/api/stats/all", name="api_user_stats_all", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getAllStats(TransactionRepository $repository, Request $request)
    {
        if($request->isXmlHttpRequest()) {
            $transactions = $repository->findAllTransactionForChart($this->getUser()->getEmail());
            $transactions = $this->dataMaker->getAllTypeData($transactions);

            return $this->json($transactions);
        }

        return $this->json(['error' => 'Bad Request'], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param TransactionRepository $repository
     * @param Request $request
     * @Route("/api/stats/date", name="api_user_stats_date", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getSpecificDateChart(TransactionRepository $repository, Request $request)
    {
        if($request->isXmlHttpRequest()) {

            $date = $request->get('date');
            $type = $request->get('type');
            $transactions = $repository->findAllTransactionsByDateForChart($this->getUser()->getEmail(),$date,$type);

            return $this->json( $transactions ? $this->dataMaker->getOneTypeData($transactions) : "");
        }
    }

    /**
     * @param TransactionRepository $repository
     * @param Request $request
     * @Route("/api/stats/year", name="api_user_stats_year", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getSpecificYearChart(TransactionRepository $repository, Request $request)
    {
        if($request->isXmlHttpRequest()) {

            $year = $request->get('year');
            $type = $request->get('type');
            $transactions = $repository->findAllTransactionByYearForChart($this->getUser()->getEmail(),$year,$type);

            return $this->json($transactions ? $this->dataMaker->getOneTypeData($transactions) : "");
        }
    }

    /**
     * @param TransactionRepository $repository
     * @param Request $request
     * @Route("/api/stats/month", name="api_user_stats_month", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getSpecificMonthChart(TransactionRepository $repository, Request $request)
    {
        if($request->isXmlHttpRequest()) {

            $month = $request->get('month');
            $type = $request->get('type');
            $transactions = $repository->findAllTransactionsByMonthForChart($this->getUser()->getEmail(),$month,$type);

            return $this->json($transactions ? $this->dataMaker->getOneTypeData($transactions) : "");
        }
    }

    /**
     * @param TransactionRepository $repository
     * @param Request $request
     * @Route("/api/stats/get/year", name="api_user_stats_get_year", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getYears(TransactionRepository $repository, Request $request)
    {
        if($request->isXmlHttpRequest()) {
            $years = $request->getSession()->get('years') ? $request->getSession()->get('years') :
                $years = $repository->findAllRelevantYears($this->getUser()->getEmail()) and $request->getSession()->set ('years',$years);

            return $this->json(['years' => $years]);
        }
    }

    /**
     * @param TransactionRepository $repository
     * @param Request $request
     * @Route("/api/stats/get/month", name="api_user_stats_get_month", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getMonths(TransactionRepository $repository, Request $request)
    {
        if($request->isXmlHttpRequest()) {

            $year = $request->query->get('year');

            $months = $request->getSession()->get('month') ? $request->getSession()->get('month') :
                $months = $repository->findAllRelevantMonths($this->getUser()->getEmail(),$year) and $request->getSession()->set ('months',$months);

            return $this->json(['months' => $this->dataMaker->formatMonthData($months)]);
        }
    }

    /**
     * @Route("/api/user/tutorial", methods={"POST"})
     */
    public function removeTutorial(EntityManagerInterface $em)
    {
        $this->getUser()->setHasTutorial(0);
        $em->flush();

        return $this->json(['success' => 1],Response::HTTP_OK);
    }

    /**
     * @Route("/api/user/compare", methods={"POST"})
     */
    public function compareCharts(Request $request,TransactionRepository $repository)
    {
        $data = json_decode($request->getContent(),true);

        $data1 = $repository->findAllMonthByYearForComparison($this->getUser()->getEmail(),$data['year1'],$data['month1'],$data['type']);
        $data2 = $repository->findAllMonthByYearForComparison($this->getUser()->getEmail(),$data['year2'],$data['month2'],$data['type']);
        $monthName1 = date("F", mktime(0, 0, 0, $data['month1'], 10));
        $monthName2 = date("F", mktime(0, 0, 0, $data['month2'], 10));
        $monthNames = ['date1' => $monthName1, 'date2' => $monthName2];
        $result = array_merge($this->dataMaker->getComparisonChartData($data1,$data2),$monthNames);

        return $this->json($result, Response::HTTP_OK);
    }

    /**
     * @Route("api/chart/change", methods={"PATCH"})
     */
    public function changeChart(Request $request, EntityManagerInterface $em)
    {

        $chartsToCheck = ['pie','bar','horizontalBar','line','radar','doughnut','polarArea'];
        $chart = $request->get('chart');
        if(!in_array($chart,$chartsToCheck)) {
            return $this->json(['error' => 'Oops! Something went wrong...'],Response::HTTP_BAD_REQUEST);
        }
        $user = $this->getUser();
        $user->setChartType($chart);
        $em->flush();

        return $this->json(['success'=>'Chart changed successfully']);
    }
}
