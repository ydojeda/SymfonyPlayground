<?php
declare(strict_types=1);

namespace App\Controller;

use App\DTO\BlogPostListEnquiry;
use App\Entity\BlogPost;
use App\Service\Serializer\DTOSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\BlogPostRepository;

class BlogPostController extends AbstractController
{

    /**
     * @Route("/blogposts/{userID?}", name="blog-posts", methods={"GET"})
     * @throws NotSupported
     * @throws \JsonException
     *
     * returns blogposts by userID or If userID null, all blogposts
     */
    public function getBlogPosts(
        Request $request,
        EntityManagerInterface $entityManager,
        DTOSerializer $serializer,
        ?int $userID = null,
    ):
    Response {
        /** @var BlogPostListEnquiry $enquiry */
        $requestContent = $request->getContent();

        $enquiry = empty($requestContent) ? new BlogPostListEnquiry() : $serializer->deserialize(
            $request->getContent(),
            BlogPostListEnquiry::class,
            'json'
        );
        /** @var BlogPostRepository $blogPostRepository */
        $blogPostRepository = $entityManager->getRepository(BlogPost::class);

        $posts = $userID === null ? $blogPostRepository->getBatchedBlogPosts(
            limit: $enquiry->getLimit(),
            offset: $enquiry->getOffset()
        ) : $blogPostRepository->findBy(['createdBy' => $userID],
            limit: $enquiry->getLimit(),
            offset: $enquiry->getOffset());

        $posts_json = json_encode($posts, JSON_THROW_ON_ERROR);

        return new Response($posts_json, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/blogposts/{userID}", name="blog-posts-delete", methods={"DELETE"})
     * @throws NotSupported
     * @throws \JsonException
     *
     */
    public function removeBlogPost(
        EntityManagerInterface $entityManager,
        int $userID,
    ):
    Response {

        /** @var BlogPostRepository $blogPostRepository */
        $blogPostRepository = $entityManager->getRepository(BlogPost::class);

        $post = $blogPostRepository->find($userID);

        if (!$post) {
            throw $this->createNotFoundException(
                'No post found for id' . $userID
            );
        }

        try {
            $blogPostRepository->removeById($userID);
        } catch (OptimisticLockException|ORMException $e) {
            return new Response(
                json_encode(["success" => true, "msg" => $e->getMessage()], JSON_THROW_ON_ERROR),
                200,
                ['Content-Type' => 'application/json']
            );

        }

        return new Response(
            json_encode(["success" => true, "removedID" => $userID], JSON_THROW_ON_ERROR),
            200,
            ['Content-Type' => 'application/json']
        );
    }

}