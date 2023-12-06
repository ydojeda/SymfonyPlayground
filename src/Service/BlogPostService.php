<?php

namespace App\Service;

use App\DTO\BlogPostEnquiry;
use App\Entity\BlogPost;
use App\Entity\BlogUser;
use App\Repository\BlogPostRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class BlogPostService
{
    public function __construct(private BlogPostRepository $repository)
    {
    }


    public function getBlogPostReturnData(BlogPost $blogPost): array
    {
        return [
            "id" => $blogPost->getId(),
            "createDate" => $blogPost->getCreateDate()->getTimestamp(),
            "userID" => $blogPost->getCreatedBy()->getId(),
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
            $this->repository->getBatchedBlogPosts($limit, $offset)
            : $this->repository->findBy(
                ['createdBy' => $userID],
                limit: $limit,
                offset: $offset
            );

    }

    /**
     * @return BlogPost
     */
    public function createBlogPostFromEnquiry(BlogPostEnquiry $enquiry, BlogUser $user): BlogPost
    {
        return (new BlogPost())
            ->setCreateDate((new \DateTime())->setTimestamp($enquiry->getTimestamp()))
            ->setCreatedBy($user)
            ->setBody($enquiry->getBody())
            ->setTags($enquiry->getTags() ?? '')
            ->setReactions(0);
    }

    public function updateBlogPostFromEnquiry(BlogPostEnquiry $enquiry, int $id): ?BlogPost
    {
        $post = $this->repository->find($id);

        return $post ? (
        $post->setBody($enquiry->getBody())
            ->setTags($enquiry->getTags())
            ->setReactions($enquiry->getReactions())
        ) : $post;
    }


}