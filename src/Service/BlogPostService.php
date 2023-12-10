<?php

namespace App\Service;

use App\DTO\BlogPostEnquiry;
use App\Entity\BlogPost;
use App\Entity\User;
use App\Repository\BlogPostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;

class BlogPostService
{
    private BlogPostRepository $blogPostRepository;

    private UserRepository $userRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        /** @var BlogPostRepository $blogPostRepository */
        $blogPostRepository = $entityManager->getRepository(BlogPost::class);
        /** @var UserRepository $userRepository */
        $userRepository = $entityManager->getRepository(User::class);

        $this->blogPostRepository = $blogPostRepository;
        $this->userRepository = $userRepository;
    }


    public function getBlogPostReturnData(BlogPost $blogPost): array
    {
        return [
            "id" => $blogPost->getId(),
            "createDate" => $blogPost->getCreateDate(),
            "userID" => $blogPost->getUser()->getId(),
            "body" => $blogPost->getBody(),
            "tags" => explode(',', $blogPost->getTags()),
            "reactions" => $blogPost->getReactions(),
        ];
    }

    /**
     * @return BlogPost[] Returns an array of BlogPost objects
     */
    // TODO: add limit and offset logic
    public function getBlogPosts(int $userID = null, int $limit = 50, int $offset = 0): array
    {
        return $userID === null ?
            $this->blogPostRepository->getBatchedBlogPosts($limit, $offset)
            : $this->blogPostRepository->findBy(
                ['user' => $userID],
                limit: $limit,
                offset: $offset
            );

    }

    /**
     * @return BlogPost
     */
    public function createBlogPostFromEnquiry(BlogPostEnquiry $enquiry): BlogPost
    {
        $user = $this->userRepository->find($enquiry->getUserId());

        if (!$user) {
            throw new Exception('Failed to create post for user', 400);
        }

        if (empty($enquiry->getBody())) {
            throw new Exception('Failed to create post. Empty post content', 400);
        }

        return (new BlogPost())
            ->setCreateDate((new \DateTime())->getTimestamp())
            ->setUser($user)
            ->setBody($enquiry->getBody())
            ->setTags($enquiry->getTags() ?? '')
            ->setReactions(0);
    }

    public function updateBlogPostFromEnquiry(BlogPostEnquiry $enquiry, int $id): ?BlogPost
    {
        $post = $this->blogPostRepository->find($id);

        return $post ? (
        $post->setBody($enquiry->getBody())
            ->setTags($enquiry->getTags())
            ->setReactions($enquiry->getReactions())
        ) : $post;
    }


}