<?php

namespace MessengerBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use UserBundle\Entity\User;

/**
 * Class UserFixtures
 *
 * @package MessengerBundle\DataFixtures
 */
class UserFixtures extends Fixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $items = [
            [
                'userName' => 'panda',
                'authToken' => '12345',
            ],
            [
                'userName' => 'lion',
                'authToken' => '67890',
            ],
            [
                'userName' => 'zebra',
                'authToken' => 'abcde',
            ],
            [
                'userName' => 'tiger',
                'authToken' => 'top-secret',
            ],
        ];

        foreach ($items as $item) {
            $user = new User($item['userName'], $item['authToken']);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
