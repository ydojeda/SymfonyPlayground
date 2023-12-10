<?php
declare(strict_types=1);

namespace App\Controller;

use App\DTO\BlogPostEnquiry;
use App\DTO\BlogPostListEnquiry;
use App\Entity\BlogPost;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\BlogPostService;
use App\Service\Serializer\DTOSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\BlogPostRepository;

class BlogPostController extends AbstractController
{
    private BlogPostService $blogPostService;
    private DTOSerializer $serializer;

    public function __construct(BlogPostService $blogPostService, DTOSerializer $serializer)
    {
        $this->blogPostService = $blogPostService;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/blogposts/{userID?}", name="blog-posts", methods={"GET"})
     * @throws NotSupported
     * @throws \JsonException
     *
     * returns blogposts by userID or If userID null, all blogposts
     */
    public function getBlogPostList(
        Request $request,
        ?int $userID = null,
    ):
    Response {
        $enquiry = (new BlogPostListEnquiry())
            ->setLimit($request->get('limit'))
            ->setOffset($request->get('offset'));

        /** @var BlogPostRepository $blogPostRepository */

        $posts = $this->blogPostService->getBlogPosts($userID, $enquiry->getLimit(), $enquiry->getOffset());

        $posts_json = json_encode(
            array_map(array($this->blogPostService, 'getBlogPostReturnData'), $posts),
            JSON_THROW_ON_ERROR
        );

        return new Response($posts_json, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/blogposts/create", name="blog-posts-create", methods={"POST"})
     * @throws \JsonException
     *
     */
    public function createBlogPost(Request $request): Response
    {
        /** @var BlogPostEnquiry $postEnquiry */
        $postEnquiry = $this->serializer->deserialize(
            $request->getContent(),
            BlogPostEnquiry::class,
            'json'
        );
        try {
            $this->blogPostService->createBlogPostFromEnquiry($postEnquiry);
        } catch (OptimisticLockException|ORMException|\Exception  $ex) {
            return new Response(
                json_encode(
                    ['success' => false, "msg" => $ex->getMessage()],
                    JSON_THROW_ON_ERROR
                ),
                $ex->getCode(),
                ['Content-Type' => 'application/json']
            );

        }

        return new Response(
            json_encode(["success" => true], JSON_THROW_ON_ERROR),
            200,
            ['Content-Type' => 'application/json']
        );
    }


    /**
     * @Route("/blogposts", name="blog-posts-update", methods={"PUT"})
     * @throws NotSupported
     * @throws \JsonException
     *
     */
    public function updateBlogPost(
        Request $request,
        EntityManagerInterface $entityManager,
    ):
    Response {
        /** @var BlogPostEnquiry $postEnquiry */
        $postEnquiry = $this->serializer->deserialize(
            $request->getContent(),
            BlogPostEnquiry::class,
            'json'
        );

        try {
            $updatedPost = $this->blogPostService->updateBlogPostFromEnquiry($postEnquiry);
        } catch (\Exception $ex) {
            return new Response(
                json_encode(
                    ['success' => false, "msg" => $ex->getMessage()],
                    JSON_THROW_ON_ERROR
                ),
                $ex->getCode(),
                ['Content-Type' => 'application/json']
            );
        }

        $entityManager->flush();

        return new Response(
            json_encode(["success" => true], JSON_THROW_ON_ERROR),
            200,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * @Route("/blogposts/{blogID}", name="blog-posts-delete", methods={"DELETE"})
     * @throws \JsonException
     */
    public function removeBlogPost(
        int $blogID,
    ):
    Response {

        try {
            $this->blogPostService->deleteBlogPost($blogID);
        } catch (OptimisticLockException|ORMException|\Exception $e) {
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


}