<?php

namespace App\DataFixtures;

use App\Entity\ApiToken;
use App\Entity\TransactionType;
use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class TransactionTypeFixture extends BaseFixture
{
    private $passwordEncoder;

    private static $type = [
      'income',
      'outcome'
    ];

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(15, 'main_transaction_types', function() use ($manager) {
            $transactionType = new TransactionType();

            $transactionType->setType($this->faker->randomElement(self::$type));
            $transactionType->setName(ucfirst($this->faker->word));

            return $transactionType;
        });

        $manager->flush();
    }
}
