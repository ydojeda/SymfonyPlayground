<?php

namespace App\Service;

use App\DTO\BlogPostEnquiry;
use App\Entity\BlogPost;
use App\Entity\User;
use App\Repository\BlogPostRepository;

class BlogPostService
{
    public function __construct(private BlogPostRepository $repository)
    {
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
    public function createBlogPostFromEnquiry(BlogPostEnquiry $enquiry, User $user): BlogPost
    {
        return (new BlogPost())
            ->setCreateDate($enquiry->getTimestamp())
            ->setUser($user)
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