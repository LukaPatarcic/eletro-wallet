<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WalletController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function homepage()
    {
        return $this->render('wallet/index.html.twig');
    }

    /**
     * @Route("/verify", name="app_verification")
     */
    public function verification()
    {
        return $this->render('wallet/verification.html.twig');
    }

    /**
     * @Route("/verify/email/{hashedEmail}", name="app_verification_email")
     */
    public function verificationEmail($hashedEmail, UserRepository $userRepository, EntityManagerInterface $em)
    {
        if($hashedEmail) {
            $user = $userRepository->findOneBy(['hashedEmail' => $hashedEmail]);
            if($user->getHashedEmail() === $hashedEmail) {
                $user->setVerified(1);
                $em->flush();
                $this->addFlash('success','Congratulations you have verified your account');
                return $this->redirectToRoute('app_login');
            }
            return $this->redirectToRoute('app_homepage');
        }
        return $this->render('mail/verify.html.twig');
    }

}
