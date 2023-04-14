<?php

namespace App\EntityListener;

use App\Entity\Blog;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsEntityListener(event: Events::prePersist, entity: Blog::class)]
#[AsEntityListener(event: Events::preUpdate, entity: Blog::class)]
class BlogEntityListener
{

    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function prePersist(Blog $blog, LifecycleEventArgs $args)
    {
        $blog->computeSlug($this->slugger);
    }

    public function preUpdate(Blog $blog, LifecycleEventArgs $args)
    {
        $blog->computeSlug($this->slugger);
    }
}