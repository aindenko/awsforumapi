<?php

namespace AppBundle\Tests\Fixtures;

use AppBundle\Entity\Post;
use AppBundle\Entity\Token;
use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class AppExistsPostsFixture implements FixtureInterface
{


    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setLogin('user1');
        $user->setName('User 1');
        $user->setPasswordHash('bla');
        $user->setCreatedAt(new \DateTime('2016-01-01'));
        $manager->persist($user);
        $manager->flush();


        $user2 = new User();
        $user2->setLogin('user2');
        $user2->setName('User 3');
        $user2->setPasswordHash('bla');
        $user2->setCreatedAt(new \DateTime('2016-01-01'));
        $manager->persist($user2);
        $manager->flush();

        $token = new Token();
        $token->setUser($user);
        $token->setHash('Hash');
        $token->setExpiredAt(new \DateTime('2030-01-01 00:00:00'));
        $manager->persist($token);
        $manager->flush();


        $post = new Post();
        $post->setImageFullUrl('http://example.com/1.jpg');
        $post->setImageThumbUrl('http://example.com/1_thumb.jpg');
        $post->setUser($user);
        $post->setViews(10);
        $post->setCreatedAt(new \DateTime('2016-01-01'));
        $post->setTitle('Test title');
        $manager->persist($post);
        $manager->flush();

        $post = new Post();
        $post->setImageFullUrl('http://example.com/2.jpg');
        $post->setImageThumbUrl('http://example.com/2_thumb.jpg');
        $post->setUser($user2);
        $post->setViews(10);
        $post->setCreatedAt(new \DateTime('2016-01-01'));
        $post->setTitle('Test title');
        $manager->persist($post);
        $manager->flush();

    }
}
