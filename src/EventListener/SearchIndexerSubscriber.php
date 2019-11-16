<?php


namespace App\EventListener;

use App\Entity\CustomTransactionType;
use App\Entity\Transaction;
use App\Entity\TransactionType;
use App\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Security\Core\Security;

class SearchIndexerSubscriber implements EventSubscriber
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
            Events::postRemove
        ];
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if($entity instanceof Transaction) {
            $user = $this->security->getUser();
            $oldAmount = $this->security->getUser()->getBalance();

            if($entity->getTransactionType()->getType() == 'income') {
                $newAmount = $oldAmount - $entity->getAmount();
            } else {
                $newAmount = $oldAmount + $entity->getAmount();
            }

            $user->setBalance($newAmount);
            $em = $args->getObjectManager();

            $em->persist($user);
            $em->flush();

            return;
        }
    }


    public function postPersist(LifecycleEventArgs $args)
    {
        // NOTE: 'cli' != php_sapi_name() ne pitaj sta je, postoji greska kada hoces da
        // trigerujes i pokrenes fixture u isto vreme. sa ovim if-om rade sve fixture osim transakcija...
        // http://symfony-world.blogspot.com/2011/06/doctrine-event-listeners-vs-fixtures.html
        if('cli' != php_sapi_name()) {
            $entity = $args->getObject();

            if($entity instanceof Transaction) {
                $user = $this->security->getUser();
                $oldAmount = $this->security->getUser()->getBalance();

                if($entity->getTransactionType()->getType() == 'income') {
                    $newAmount = $oldAmount + $entity->getAmount();
                } else {
                    $newAmount = $oldAmount - $entity->getAmount();
                }

                $user->setBalance($newAmount);
                $em = $args->getObjectManager();

                $em->persist($user);
                $em->flush();

                return;
            }

            if($entity instanceof TransactionType) {
                $user = $this->security->getUser();
                $em = $args->getObjectManager();

                $custom = new CustomTransactionType();
                $custom->setUser($em->find(User::class, $user->getId()));
                $custom->setTransactionType($entity);

                $em->persist($custom);
                $em->flush();

                return;
            }
        }
    }
}