<?php
declare(strict_types=1);

namespace App\Controller;

use App\DTO\BlogPostEnquiry;
use App\DTO\BlogPostListEnquiry;
use App\Entity\BlogPost;
use App\Entity\BlogUser;
use App\Repository\BlogUserRepository;
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
        $postText = $postEnquiry->getBody();
        $postTimestamp = $postEnquiry->getTimestamp();


        /** @var BlogUserRepository $blogPostRepository */
        $blogUserRepository = $entityManager->getRepository(BlogUser::class);

        $user = $blogUserRepository->find($userID);

        if (!$user) {
            throw $this->createNotFoundException(
                'No user found for id' . $userID
            );
        }

        if (empty($postText)) {
            return new Response(
                json_encode(["success" => false, "msg" => "Missing body text"], JSON_THROW_ON_ERROR),
                400,
                ['Content-Type' => 'application/json']
            );
        }

        if ($postTimestamp === null) {
            return new Response(
                json_encode(["success" => false, "msg" => "Missing timestamp text"], JSON_THROW_ON_ERROR),
                400,
                ['Content-Type' => 'application/json']
            );
        }

        $post = (new BlogPost())
            ->setCreateDate((new \DateTime())->setTimestamp($postTimestamp))
            ->setCreatedBy($user)
            ->setBody($postText)
            ->setTags($postEnquiry->getTags() ?? '')
            ->setReactions($postEnquiry->getReactions() ?? 0);


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
        $post = $entityManager->getRepository(BlogPost::class)->find($blogID);

        if (!$post) {
            throw $this->createNotFoundException(
                'No post found for id ' . $blogID
            );
        }

        $post->setBody($postEnquiry->getBody())
            ->setTags($postEnquiry->getTags())
            ->setReactions($postEnquiry->getReactions());
        $entityManager->flush();

        return new Response(
            json_encode(["success" => true, "updatedID" => $blogID], JSON_THROW_ON_ERROR),
            200,
            ['Content-Type' => 'application/json']
        );
    }


}