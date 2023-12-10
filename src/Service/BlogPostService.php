<?php

namespace App\Service;

use App\DTO\BlogPostEnquiry;
use App\Entity\BlogPost;
use App\Entity\User;
use App\Repository\BlogPostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
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
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createBlogPostFromEnquiry(BlogPostEnquiry $enquiry): void
    {
        $user = $this->userRepository->find($enquiry->getUserId());

        if (!$user) {
            throw new Exception('Failed to create post for user', 400);
        }

        if (empty($enquiry->getBody())) {
            throw new Exception('Failed to create post. Empty post content', 400);
        }

        $newPost = (new BlogPost())
            ->setCreateDate((new \DateTime())->getTimestamp())
            ->setUser($user)
            ->setBody($enquiry->getBody())
            ->setTags($enquiry->getTags() ?? '')
            ->setReactions(0);

        $this->blogPostRepository->add($newPost);
    }

    public function updateBlogPostFromEnquiry(BlogPostEnquiry $enquiry): void
    {
        $post = $this->blogPostRepository->find($enquiry->getUserId());

        if (!$post) {
            throw new Exception('Failed to update post', 400);
        }

        $post->setBody($enquiry->getBody())
            ->setTags($enquiry->getTags())
            ->setReactions($enquiry->getReactions());
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function deleteBlogPost(int $blogId): void
    {
        $post = $this->blogPostRepository->find($blogId);

        if (!$post) {
            throw new Exception('Failed to delete post', 400);
        }

        $this->blogPostRepository->remove($post);


    }


}