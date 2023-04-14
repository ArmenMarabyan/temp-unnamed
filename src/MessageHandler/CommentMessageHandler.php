<?php

namespace App\MessageHandler;

use App\Message\CommentMessage;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CommentMessageHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CommentRepository $commentRepository,
    ) {
    }

    public function __invoke(CommentMessage $message)
    {
        $comment = $this->commentRepository->find($message->getId());
        if (!$comment) {
            return;
        }

        if (2 === mt_rand(1, 5)) {
            $comment->setState('spam');
        } else {
            $comment->setState('published');
        }

        $this->entityManager->flush();
    }
}