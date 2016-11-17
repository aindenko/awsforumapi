<?php

namespace AppBundle\Tests\Fixtures;

use AppBundle\Entity\Post;
use AppBundle\Entity\Token;
use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class AppExistsPostsFixture implements FixtureInterface
{

    public function setUp()
    {
        $this->loadFixtures(array(
            'AppBundle\Fixtures\AppExistsPostsFixture',
        ));
    }

    public function load(ObjectManager $manager)
    {
        $token = new Token();
        $token->setUser(1);
        $token->setHash('Hash');
        $token->setExpiredAt('2030-01-01 00:00:00');
        $manager->persist($token);
        $manager->flush();

        $user = new User();
        $user->setLogin('user1');
        $user->setCreatedAt('2016-01-01');
        $manager->persist($user);
        $manager->flush();

        $post = new Post();
        $post->setImageFullUrl('http://example.com/1.jpg');
        $post->setImageThumbUrl('http://example.com/1_thumb.jpg');
        $post->setUser(1);
        $post->setViews(10);
        $post->setCreatedAt('2016-01-01');
        $post->setTitle('Test title');
        $manager->persist($post);
        $manager->flush();

        $post = new Post();
        $post->setImageFullUrl('http://example.com/1.jpg');
        $post->setImageThumbUrl('http://example.com/1_thumb.jpg');
        $post->setUser(1);
        $post->setViews(10);
        $post->setCreatedAt('2016-01-01');
        $post->setTitle('Test title');
        $manager->persist($post);
        $manager->flush();

    }
}
