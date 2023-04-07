<?php

namespace App\Controller\Blog;

use App\Entity\Blog;
use App\Repository\BlogRepository;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    public function __construct(private BlogRepository $blogRepository, private CommentRepository $commentRepository)
    {
    }

    #[Route('/blog', name: 'app_blog')]
    public function index(): Response
    {
        $blogItems = $this->blogRepository->findAll();

        return $this->render('blog/index.html.twig', [
            'items' => $blogItems,
        ]);
    }

    #[Route('/blog/{id}', name: 'app_blog_show')]
    public function show(Blog $blog): Response
    {
//        $blogItem = $this->blogRepository->findBy(['id' => $id]);

//        dump($blog->getComments());die;

        $comments = $this->commentRepository->getCommentPaginator($blog, 10);
//        dump($comments);die;

        return $this->render('blog/show.html.twig', [
            'item' => $blog,
            'comments' => $comments
        ]);
    }
}
