<?php

declare(strict_types=1);

namespace Cheeper\Chapter7\Infrastructure\DomainModel\Author;

use Cheeper\AllChapters\DomainModel\Author\AuthorId;
use Cheeper\AllChapters\DomainModel\Author\UserName;
use Cheeper\Chapter7\DomainModel\Author\Author;
use Cheeper\Chapter7\DomainModel\Author\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;

//snippet doctrine-orm-authors
final class DoctrineOrmAuthorRepository implements AuthorRepository
{
    public function __construct(
        private EntityManagerInterface $em
    ) {
    }

    public function ofId(AuthorId $authorId): Author|null
    {
        return $this->em
            ->getRepository(Author::class)
            ->findOneBy([
                'authorId' => $authorId->id(),
            ]);
    }

    public function ofUserName(UserName $userName): Author|null
    {
        return $this->em
            ->getRepository(Author::class)
            ->findOneBy(['userName' => $userName->userName()]);
    }

    public function add(Author $author): void
    {
        $this->em->persist($author);
    }
}
//end-snippet
