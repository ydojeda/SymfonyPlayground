<?php
declare(strict_types=1);

namespace App\Controller;

use App\DTO\BlogPostEnquiry;
use App\DTO\BlogPostListEnquiry;
use App\Service\BlogPostService;
use App\Service\Serializer\DTOSerializer;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\BlogPostRepository;

class BlogPostController extends AbstractController
{

    public function __construct(private BlogPostService $blogPostService, private DTOSerializer $serializer)
    {
    }

    /**
     * @Route("/blogposts/{userID?}", name="blog-posts", methods={"GET"})
     * returns blogposts by userID or If userID null, all blogposts
     */
    public function getBlogPostList(Request $request, ?int $userID = null): Response
    {
        $enquiry = (new BlogPostListEnquiry())
            ->setLimit($request->get('limit'))
            ->setOffset($request->get('offset'));

        /** @var BlogPostRepository $blogPostRepository */

        $posts = $this->blogPostService->getBlogPosts($userID, $enquiry->getLimit(), $enquiry->getOffset());


        return new JsonResponse(
            array_map(array($this->blogPostService, 'getBlogPostReturnData'), $posts),
            200,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * @Route("/blogposts/create", name="blog-posts-create", methods={"POST"})
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
            return new JsonResponse(
                ['success' => false, "msg" => $ex->getMessage()],
                $ex->getCode(),
                ['Content-Type' => 'application/json']
            );

        }

        return new JsonResponse(
            ["success" => true],
            200,
            ['Content-Type' => 'application/json']
        );
    }


    /**
     * @Route("/blogposts", name="blog-posts-update", methods={"PUT"})
     */
    public function updateBlogPost(Request $request):
    Response {
        /** @var BlogPostEnquiry $postEnquiry */
        $postEnquiry = $this->serializer->deserialize(
            $request->getContent(),
            BlogPostEnquiry::class,
            'json'
        );

        try {
            $this->blogPostService->updateBlogPostFromEnquiry($postEnquiry);
        } catch (OptimisticLockException|ORMException|\Exception $ex) {
            return new JsonResponse(
                ['success' => false, "msg" => $ex->getMessage()],
                $ex->getCode(),
                ['Content-Type' => 'application/json']
            );
        }


        return new JsonResponse(
            ["success" => true],
            200,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * @Route("/blogposts/{blogID}", name="blog-posts-delete", methods={"DELETE"})
     */
    public function removeBlogPost(int $blogID):
    Response {

        try {
            $this->blogPostService->deleteBlogPost($blogID);
        } catch (OptimisticLockException|ORMException|\Exception $e) {
            return new JsonResponse(
                ["success" => true, "msg" => $e->getMessage()],
                200,
                ['Content-Type' => 'application/json']
            );

        }

        return new JsonResponse(
            ["success" => true, "removedID" => $blogID],
            200,
            ['Content-Type' => 'application/json']
        );
    }


}