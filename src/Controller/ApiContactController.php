<?php

namespace App\Controller;

use App\Services\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiContactController extends AbstractController
{
    /**
     * @Route("/api/contact", name="api_contact")
     */
    public function index(Mailer $mailer, Request $request)
    {
        if($request->isXmlHttpRequest()) {
            $name = $request->request->get('name');
            $email = $request->request->get('email');
            $subject = $request->request->get('subject');
            $message = $request->request->get('message');

            $send = $mailer->send($email,$name,$subject,$message);

            return $this->json (['sent' => $send]);
        }

    }
}
