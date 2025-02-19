<?php

declare(strict_types=1);

namespace Cheeper\Chapter4\Application\Cheep\Command;

use Cheeper\AllChapters\DomainModel\Cheep\CheepDoesNotExist;
use Cheeper\AllChapters\DomainModel\Cheep\CheepId;
use Cheeper\AllChapters\DomainModel\Cheep\CheepMessage;
use Cheeper\Chapter4\DomainModel\Cheep\CheepRepository;

//snippet recompose-cheep-handler
final class UpdateCheepMessageCommandHandler
{
    public function __construct(
        private CheepRepository $cheepRepository
    ) {
    }

    public function __invoke(UpdateCheepMessageCommand $message): void
    {
        $cheepId = CheepId::fromString($message->cheepId());
        $cheepMessage = CheepMessage::write($message->message());

        $cheep = $this->cheepRepository->ofId($cheepId);

        if (null === $cheep) {
            throw CheepDoesNotExist::withIdOf($cheepId);
        }

        $cheep->recomposeWith($cheepMessage);
    }
}
//end-snippet
