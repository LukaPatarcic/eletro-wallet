<?php

namespace App\DataFixtures;

use App\Entity\ApiToken;
use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends BaseFixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(1, 'main_users', function($i) use ($manager) {
            $user = new User();
            $user->setEmail(sprintf('undefined%s@gmail.com', $i));
            $user->setHashedEmail();
            $user->setVerified(1);
            $user->setHasTutorial(0);
            $user->setProfileName($this->faker->userName);

            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                'punopetica'
            ));

            return $user;
        });

        $manager->flush();
    }
}
