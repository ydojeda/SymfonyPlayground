<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\BlogUser;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    /**
     * @throws \JsonException
     */
    public function load(ObjectManager $manager): void
    {
        $json_data = file_get_contents(__DIR__ . '/testdata.json');
        $json_data = json_decode($json_data, true, 512, JSON_THROW_ON_ERROR);
        $users= $json_data['users'];
        $posts = $json_data['posts'];
        /** @var $user_list BlogUser[] */
        $user_list = [];

        foreach ($users as $data) {
            $user = new BlogUser();
            $user->setFirstName($data["firstName"]);
            $user->setLastName($data["lastName"]);
            $user->setAddress($data["address"]["state"]);
            $user->setCompany($data["company"]["name"]);
            $user->setEmail($data["email"]); # TODO: unique constraint
            $user->setImage($data["image"]);
            $user_list[] = $user;
            $manager->persist($user);
        }

        foreach ($posts as $data) {
            $post = new BlogPost();
            $tags = $data["tags"] ?? [];

            $post->setBody($data["body"]);
            $post->setTags(implode(',', $tags));
            $post->setReactions($data["reactions"]);

            # Get a random date
            $dateToday = (new DateTime());
            $dateMin = $dateToday->setTimestamp(0);
            $createDate = (new DateTime())->setTimestamp(random_int($dateMin->getTimestamp(), $dateToday->getTimestamp()));

            $post->setCreateDate($createDate);

            # Get a random user
            $post->setCreatedBy($user_list[array_rand($user_list)]);
            $manager->persist($post);

        }
        $manager->flush();
    }
}
