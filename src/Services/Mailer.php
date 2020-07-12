<?php


namespace App\Services;

use App\Entity\User;
use Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension;
use Twig\Environment;
use Swift_Mailer;

class Mailer
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var TwigExtension
     */
    private $twig;

    public function __construct(Environment $twig, Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function send(string $email,string $name,string $subject,string $message)
    {
        // Create a message
        $emailMessage = (new \Swift_Message('Verification Email'))
            ->setFrom([$email => $name])
            ->setTo('patarcic98@gmail.com')
            ->setSubject($subject)
            ->setBody($message,'text/html')
        ;

        return $this->mailer->send($emailMessage);
    }

    public function verification(User $user)
    {
        // Create a message
        $emailMessage = (new \Swift_Message('Verification Email'))
            ->setFrom(['luka@lukaku.tech' => 'Electro Wallet'])
            ->setTo($user->getEmail())
            ->setSubject('Account Verification')
            ->setBody($this->twig->render('mail/verify.html.twig',['email' => md5($user->getEmail())]),'text/html')
        ;

        return $this->mailer->send($emailMessage);
    }
}