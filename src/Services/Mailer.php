<?php


namespace App\Services;

use App\Entity\User;
use Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension;
use Twig\Environment;

class Mailer
{
    /**
     * @var \Swift_SmtpTransport
     */
    private $transport;
    /**
     * @var TwigExtension
     */
    private $twig;

    public function __construct(Environment $twig)
    {
        // Create the Transport
        $transport = (new \Swift_SmtpTransport(
            'smtp.gmail.com',
            '587',
            'tls'
        ))
            ->setUsername('lukaphptesting@gmail.com')
            ->setPassword('undefined2019;')
        ;

        $this->transport = $transport;
        $this->twig = $twig;
    }

    public function send(string $email,string $name,string $subject,string $message)
    {
        $mailer = new \Swift_Mailer($this->transport);
        // Create a message
        $emailMessage = (new \Swift_Message('Verification Email'))
            ->setFrom([$email => $name])
            ->setTo('patarcic98@gmail.com')
            ->setSubject($subject)
            ->setBody($message,'text/html')
        ;

        return $mailer->send($emailMessage);
    }

    public function verification(User $user)
    {
        $mailer = new \Swift_Mailer($this->transport);
        // Create a message
        $emailMessage = (new \Swift_Message('Verification Email'))
            ->setFrom(['electro@wallet.com' => 'Electro Wallet'])
            ->setTo($user->getEmail())
            ->setSubject('Account Verification')
            ->setBody($this->twig->render('mail/verify.html.twig',['email' => md5($user->getEmail())]),'text/html')
        ;

        return $mailer->send($emailMessage);
    }
}