<?php
declare(strict_types=1);

namespace App\Controller;

use App\DTO\BlogPostListEnquiry;
use App\Entity\BlogPost;
use App\Entity\BlogUser;
use App\Repository\BlogUserRepository;
use App\Service\BlogPostService;
use App\Service\Serializer\DTOSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\NotSupported;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\BlogPostRepository;

class BlogPostController extends AbstractController
{

    /**
     * @Route("/blogposts", name="blog-posts")
     * @throws NotSupported
     * @throws \JsonException
     */
    public function getBlogPosts(
        Request $request,
        EntityManagerInterface $entityManager,
        DTOSerializer $serializer,
    ):
    Response {
        /** @var BlogPostListEnquiry $enquiry */
        $enquiry = $serializer->deserialize($request->getContent(), BlogPostListEnquiry::class, 'json');

        /** @var BlogPostRepository $blogPostRepository */
        $blogPostRepository = $entityManager->getRepository(BlogPost::class);
        $posts = $blogPostRepository->findBy([], limit: $enquiry->getLimit(), offset: $enquiry->getOffset());
        $posts_json = json_encode($posts, JSON_THROW_ON_ERROR);

        return new Response($posts_json, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/blogposts/{userID}", name="blog-posts")
     * @throws NotSupported
     * @throws \JsonException
     */
    public function getBlogPostsByUser(
        Request $request,
        EntityManagerInterface $entityManager,
        DTOSerializer $serializer,
        int $userID,
    ):
    Response {
        /** @var BlogPostListEnquiry $enquiry */
        $enquiry = $serializer->deserialize($request->getContent(), BlogPostListEnquiry::class, 'json');
        /** @var BlogPostRepository $blogPostRepository */
        $blogPostRepository = $entityManager->getRepository(BlogPost::class);
        $posts = $blogPostRepository->findBy(['createdBy' => $userID],
            limit: $enquiry->getLimit(),
            offset: $enquiry->getOffset());
        $posts_json = json_encode($posts, JSON_THROW_ON_ERROR);

        return new Response($posts_json, 200, ['Content-Type' => 'application/json']);
    }

}