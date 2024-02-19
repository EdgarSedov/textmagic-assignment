<?php

declare(strict_types=1);

namespace App\Service\Dto;

readonly class SubmittedAnswerDto
{
    /**
     * @param array<string>|string $answer Array of selected options ids for multi-select, selected option id for select, string value for simple input
     */
    public function __construct(
        public int $userAnswerId,
        public array|string $answer,
    ) {
    }
}
