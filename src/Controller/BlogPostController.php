<?php
declare(strict_types=1);

namespace App\Controller;

use App\DTO\BlogPostEnquiry;
use App\DTO\BlogPostListEnquiry;
use App\Entity\BlogPost;
use App\Entity\BlogUser;
use App\Repository\BlogUserRepository;
use App\Service\BlogPostService;
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
    public function getBlogPostList(
        Request $request,
        EntityManagerInterface $entityManager,
        DTOSerializer $serializer,
        ?int $userID = null,
    ):
    Response {
        /** @var BlogPostListEnquiry $enquiry */
        $enquiry = $serializer->deserialize(
            $request->getContent(),
            BlogPostListEnquiry::class,
            'json'
        );
        /** @var BlogPostRepository $blogPostRepository */
        $blogPostRepository = $entityManager->getRepository(BlogPost::class);
        $blogPostService = new BlogPostService($blogPostRepository);

        $posts = $blogPostService->getBlogPosts($userID, $enquiry->getLimit(), $enquiry->getOffset());

        $posts_json = json_encode(
            array_map(array($blogPostService, 'getBlogPostReturnData'), $posts),
            JSON_THROW_ON_ERROR
        );

        return new Response($posts_json, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/blogposts/{userID}/create", name="blog-posts-create", methods={"POST"})
     * @throws NotSupported
     * @throws \JsonException
     *
     */
    public function createBlogPost(
        Request $request,
        EntityManagerInterface $entityManager,
        DTOSerializer $serializer,
        int $userID,
    ):
    Response {
        /** @var BlogPostEnquiry $postEnquiry */
        $postEnquiry = $serializer->deserialize(
            $request->getContent(),
            BlogPostEnquiry::class,
            'json'
        );

        /** @var BlogUserRepository $blogPostRepository */
        $blogUserRepository = $entityManager->getRepository(BlogUser::class);
        $user = $blogUserRepository->find($userID);

        /** @var BlogPostRepository $blogPostRepository */
        $blogPostRepository = $entityManager->getRepository(BlogPost::class);
        $blogPostService = new BlogPostService($blogPostRepository);

        if (!$user) {
            throw $this->createNotFoundException(
                'No user found for id' . $userID
            );
        }

        if (empty($postEnquiry->getBody())) {
            return new Response(
                json_encode(["success" => false, "msg" => "Missing body text"], JSON_THROW_ON_ERROR),
                400,
                ['Content-Type' => 'application/json']
            );
        }

        if ($postEnquiry->getTimestamp() === null) {
            return new Response(
                json_encode(["success" => false, "msg" => "Missing timestamp text"], JSON_THROW_ON_ERROR),
                400,
                ['Content-Type' => 'application/json']
            );
        }

        $post = $blogPostService->createBlogPostFromEnquiry($postEnquiry, $user);


        $entityManager->persist($post);
        $entityManager->flush();

        return new Response(
            json_encode(["success" => true], JSON_THROW_ON_ERROR),
            200,
            ['Content-Type' => 'application/json']
        );
    }


    /**
     * @Route("/blogposts/{blogID}", name="blog-posts-delete", methods={"DELETE"})
     * @throws NotSupported
     * @throws \JsonException
     *
     */
    public function removeBlogPost(
        EntityManagerInterface $entityManager,
        int $blogID,
    ):
    Response {

        /** @var BlogPostRepository $blogPostRepository */
        $blogPostRepository = $entityManager->getRepository(BlogPost::class);

        $post = $blogPostRepository->find($blogID);

        if (!$post) {
            throw $this->createNotFoundException(
                'No post found for id' . $blogID
            );
        }

        try {
            $blogPostRepository->removeById($blogID);
        } catch (OptimisticLockException|ORMException $e) {
            return new Response(
                json_encode(["success" => true, "msg" => $e->getMessage()], JSON_THROW_ON_ERROR),
                200,
                ['Content-Type' => 'application/json']
            );

        }

        return new Response(
            json_encode(["success" => true, "removedID" => $blogID], JSON_THROW_ON_ERROR),
            200,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * @Route("/blogposts/{blogID}", name="blog-posts-update", methods={"PUT"})
     * @throws NotSupported
     * @throws \JsonException
     *
     */
    public function updateBlogPost(
        Request $request,
        EntityManagerInterface $entityManager,
        DTOSerializer $serializer,
        int $blogID,
    ):
    Response {
        /** @var BlogPostEnquiry $postEnquiry */
        $postEnquiry = $serializer->deserialize(
            $request->getContent(),
            BlogPostEnquiry::class,
            'json'
        );
        /** @var BlogPostRepository $blogPostRepository */
        $blogPostRepository = $entityManager->getRepository(BlogPost::class);
        $blogPostService = new BlogPostService($blogPostRepository);

        $updatedPost = $blogPostService->updateBlogPostFromEnquiry($postEnquiry, $blogID);


        if (!$updatedPost) {
            throw $this->createNotFoundException(
                'No post found for id ' . $blogID
            );
        }

        $entityManager->flush();

        return new Response(
            json_encode(["success" => true, "updatedID" => $blogID], JSON_THROW_ON_ERROR),
            200,
            ['Content-Type' => 'application/json']
        );
    }


}