<?php

namespace App\DataFixtures;

use App\Entity\Transaction;
use App\Entity\TransactionType;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class TransactionFixture extends BaseFixture implements DependentFixtureInterface
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(500, 'main_transactions', function() use ($manager) {
            $transaction = new Transaction();

            $transaction->setAmount($this->faker->numberBetween(100,100000));
            $transaction->setCreatedAt($this->faker->dateTimeBetween('-2 years','-1 seconds'));
            $users = $this->getRandomReferences('main_users',5);
            $transactionTypes = $this->getRandomReferences('main_transaction_types',5);
            foreach ($users as $user) {
                $transaction->setUser($user);
            }
            foreach ($transactionTypes as $transactionType) {
                $transaction->setTransactionType($transactionType);
            }

            return $transaction;
        });

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
          UserFixture::class,
          TransactionTypeFixture::class,
        ];
    }
}
