<?php

declare(strict_types=1);

namespace Cheeper\Tests\Application;

use Cheeper\Application\AuthorApplicationService;
use Cheeper\DomainModel\Author\Author;
use Cheeper\DomainModel\Author\AuthorAlreadyExists;
use Cheeper\DomainModel\Author\AuthorRepository;
use Cheeper\DomainModel\Author\UserName;
use Cheeper\Infrastructure\Persistence\InMemoryAuthorRepository;
use Cheeper\Tests\DomainModel\Author\AuthorTestDataBuilder;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class AuthorApplicationServiceTest extends TestCase
{
    /** @test */
    public function givenAuthorSignUpWhenAuthorUsernameIsAlreadyPickedUpThenAnExceptionShouldBeThrown(): void
    {
        $this->expectException(AuthorAlreadyExists::class);

        $id = Uuid::uuid4()->toString();
        $username = 'irrelevant';
        $email = 'test@gmail.com';
        $name = 'irrelevant';
        $biography = 'biography';
        $location = 'location';
        $website = 'https://google.com';
        $birthDate = (new \DateTimeImmutable())->format('Y-m-d');

        $authorRepository = new InMemoryAuthorRepository();
        $authorRepository->add(
            AuthorTestDataBuilder::anAuthor()->build()
        );

        $authorApplicationService = new AuthorApplicationService($authorRepository);
        $authorApplicationService->signUp(
            $id,
            $username,
            $email,
            $name,
            $biography,
            $location,
            $website,
            $birthDate,
        );
    }

    /** @test */
    public function givenAuthorSignUpWhenExecutedWithValidDataThenANewAuthorShouldBeCreated(): void
    {
        $id = Uuid::uuid4()->toString();
        $username = 'irrelevant';
        $email = 'test@gmail.com';
        $name = 'irrelevant';
        $biography = 'biography';
        $location = 'location';
        $website = 'https://google.com';
        $birthDate = (new \DateTimeImmutable())->format('Y-m-d');

        $authorRepository = new InMemoryAuthorRepository();

        $authorApplicationService = new AuthorApplicationService($authorRepository);
        $authorApplicationService->signUp(
            $id,
            $username,
            $email,
            $name,
            $biography,
            $location,
            $website,
            $birthDate,
        );

        $this->assertNotNull(
            $authorRepository->ofUserName(UserName::pick('irrelevant'))
        );
    }
}