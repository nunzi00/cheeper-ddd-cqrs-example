<?php

declare(strict_types=1);

namespace App\Controller\Chapter4\NoCommandBus;

use Cheeper\Application\Command\Cheep\PostCheep;
use Cheeper\Application\Command\Cheep\PostCheepHandler;
use Cheeper\Chapter6\Infrastructure\Application\Event\SymfonyEventBus;
use Cheeper\DomainModel\Author\AuthorDoesNotExist;
use Cheeper\Infrastructure\Persistence\DoctrineOrmAuthors;
use Cheeper\Infrastructure\Persistence\DoctrineOrmCheeps;
use InvalidArgumentException;
use Monolog\Handler\StreamHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\MessageBusInterface;

final class ComplexPostCheepController extends AbstractController
{
    public function __invoke(Request $request, MessageBusInterface $eventBus): Response
    {
        //snippet complex-command-handler-execution
        //ignore
        $authorId = $request->request->get('author_id');
        $cheepId = $request->request->get('cheep_id');
        $message = $request->request->get('message');

        if (null === $authorId || null === $cheepId || null === $message) {
            throw new BadRequestHttpException('Invalid parameters given');
        }

        $command = PostCheep::fromArray([
            'author_id' => $authorId,
            'cheep_id' => $cheepId,
            'message' => $message,
        ]);
        //end-ignore

        $connection = \Doctrine\DBAL\DriverManager::getConnection([/** ... */]);
        $entityManager = \Doctrine\ORM\EntityManager::create(
            $connection,
            new \Doctrine\ORM\Configuration()
        );

        $postCheepHandler = new PostCheepHandler(
            new DoctrineOrmAuthors($entityManager),
            new DoctrineOrmCheeps($entityManager),
            new SymfonyEventBus($eventBus),
        );

        $logger = new \Monolog\Logger(
            'dispatched-commands',
            [new StreamHandler('/var/logs/app/commands.log')]
        );

        try {
            $logger->info('Executing signup command');
            $entityManager->wrapInTransaction(function () use ($command, $postCheepHandler, $logger): void {
                ($postCheepHandler)($command);
                $logger->info('Signup command executed successfully');
            });
        } catch (AuthorDoesNotExist | InvalidArgumentException $exception) {
            $logger->error('Signup command failed');
            throw new BadRequestHttpException($exception->getMessage(), $exception);
        }
        //end-snippet

        return new Response('', Response::HTTP_CREATED);
    }
}
