<?php

namespace App\Controller\Blog;

use App\Entity\Blog;
use App\Entity\Comment;
use App\Form\CommentFormType;
use App\Message\CommentMessage;
use App\Repository\BlogRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    public function __construct(
        private BlogRepository $blogRepository,
        private CommentRepository $commentRepository,
        private EntityManagerInterface $entityManager,
        private MessageBusInterface $bus
    ) {
    }

    #[Route('/blog', name: 'app_blog')]
    public function index(): Response
    {
        $blogItems = $this->blogRepository->findAll();

        return $this->render('blog/index.html.twig', [
            'items' => $blogItems,
        ]);
    }

    #[Route('/blog/{slug}', name: 'app_blog_show')]
    public function show(
        Blog    $blog,
        Request $request,
        #[Autowire('%photo_dir%')] string $photoDir
    ): Response
    {

        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setBlog($blog);

            if ($photo = $form['photo']->getData()) {
                $filename = bin2hex(random_bytes(6)) . '.' . $photo->guessExtension();
                try {
                    $photo->move($photoDir, $filename);
                } catch (FileException $e) {
                    // unable to upload the photo, give up
                }
                $comment->setPhotoFilename($filename);
            }

            $context = [
                'user_ip' => $request->getClientIp(),
                'user_agent' => $request->headers->get('user-agent'),
                'referrer' => $request->headers->get('referer'),
                'permalink' => $request->getUri(),
            ];

            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            $this->bus->dispatch(new CommentMessage($comment->getId(), $context));

            return $this->redirectToRoute('app_blog_show', ['slug' => $blog->getSlug()]);
        }

        $comments = $this->commentRepository->getCommentPaginator($blog, 10);

        return $this->render('blog/show.html.twig', [
            'item' => $blog,
            'comments' => $comments,
            'comment_form' => $form
        ]);
    }
}
