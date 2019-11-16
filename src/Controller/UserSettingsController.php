<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class UserSettingsController
 * @package App\Controller
 */
class UserSettingsController extends AbstractController
{
    /**
     * @Route("/user/change/username", name="user_username_change")
     */
    public function changeUsername(Request $request, EntityManagerInterface $em)
    {
        if($request->isMethod('post')) {
            $username = $request->get('username');
            if($username) {
                $user = $this->getUser();
                $user->setProfileName($username);
                $em->flush();
                $this->addFlash('success', 'Username changed successfully');
                return $this->redirectToRoute('app_dashboard_profile');
            }
        }

        return $this->render('user_settings/index.html.twig',[
            'type' => 'username'
        ]);
    }

    /**
     * @Route("/user/change/email", name="user_email_change")
     */
    public function changeEmail(Request $request, EntityManagerInterface $em, ValidatorInterface $validator, UserRepository $repository)
    {
        if($request->isMethod('post')) {
            $email = $request->get('email');
            $emailVerify = $repository->findOneBy(['email' => $email]);
            if($emailVerify) {
                $this->addFlash('error', 'Email already in use');
                return $this->render('user_settings/index.html.twig',['type' => 'email']);
            }
            if($this->validateEmail($email,$validator)) {
                $user = $this->getUser();
                $user->setEmail($email);
                $em->flush();
                $this->addFlash('success', 'Email changed successfully');
                return $this->redirectToRoute('app_dashboard_profile');
            }
            $this->addFlash('error', 'Email is not valid');
        }
        return $this->render('user_settings/index.html.twig',['type' => 'email']);
    }

    /**
     * @Route("/user/change/password", name="user_password_change")
     */
    public function changePassword(Request $request,UserPasswordEncoderInterface $encoder, EntityManagerInterface $em)
    {

        if($request->isMethod('post')) {
            $oldPassword = $request->get('oldPassword');
            $password1 = $request->get('password1');
            $password2 = $request->get('password2');
            $user = $this->getUser();
            if(!$encoder->isPasswordValid($user,$oldPassword)) {
                $this->addFlash('error', 'This password does not match old password');
                return $this->render('user_settings/index.html.twig',['type' => 'password']);
            }
            if($password1 !== $password2) {
                $this->addFlash('error', 'The passwords do not match...');
                return $this->render('user_settings/index.html.twig',[
                    'type' => 'password'
                ]);
            }

            $passwordHash = $encoder->encodePassword($user, $password1);
            $user->setPassword($passwordHash);

            $em->flush();

            $this->addFlash('success', 'Successfully changed password');
            return $this->redirectToRoute('app_dashboard_profile');
        }

        return $this->render('user_settings/index.html.twig',['type' => 'password']);
    }

    private function validateEmail(string $email, ValidatorInterface $validator): ?string
    {
        $emailConstraint = new Assert\Email();
        $errors = $validator->validate($email, $emailConstraint);
        if (0 === count($errors)) {
            return $email;
        }
        return null;
    }
}
