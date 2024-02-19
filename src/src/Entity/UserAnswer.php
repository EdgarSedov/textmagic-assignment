<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\QuestionType;
use App\Repository\UserAnswerRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use RuntimeException;
use Throwable;

#[ORM\Entity(repositoryClass: UserAnswerRepository::class)]
#[ORM\Table(name: "user_answers")]
class UserAnswer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::BIGINT)]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userAnswers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?QuestionnaireAttempt $questionnaireAttempt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Question $question = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $sequenceNumber = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $answer = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $answeredAt = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isCorrect = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestionnaireAttempt(): ?QuestionnaireAttempt
    {
        return $this->questionnaireAttempt;
    }

    public function setQuestionnaireAttempt(?QuestionnaireAttempt $questionnaireAttempt): static
    {
        $this->questionnaireAttempt = $questionnaireAttempt;

        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): static
    {
        $this->question = $question;

        return $this;
    }

    public function getSequenceNumber(): ?int
    {
        return $this->sequenceNumber;
    }

    public function setSequenceNumber(int $sequenceNumber): static
    {
        $this->sequenceNumber = $sequenceNumber;

        return $this;
    }

    /**
     * @return array<int>|int|string|null
     * @throws Exception
     */
    public function getAnswer(): array|int|string|null
    {
        if ($this->answer === null) {
            return null;
        }

        $question = $this->getQuestion();

        return match ($question->getType()) {
            QuestionType::MULTI_SELECT => json_decode($this->answer, false, 512, JSON_THROW_ON_ERROR),
            QuestionType::SELECT => (int) $this->answer,
            QuestionType::INPUT => $this->answer,
            default => throw new RuntimeException('Unsupported question type: ' . $question->getType()->value)
        };
    }

    /**
     * @param array<string>|string $answer
     */
    public function setAnswer(array|string $answer): static
    {
        $question = $this->getQuestion();

        $this->answer = match ($question->getType()) {
            QuestionType::MULTI_SELECT => json_encode($answer),
            QuestionType::SELECT, QuestionType::INPUT => $answer,
            default => throw new RuntimeException('Unsupported question type: ' . $question->getType()->value)
        };

        return $this;
    }

    public function getAnsweredAt(): ?DateTimeImmutable
    {
        return $this->answeredAt;
    }

    public function setAnsweredAt(?DateTimeImmutable $answeredAt): static
    {
        $this->answeredAt = $answeredAt;

        return $this;
    }

    public function isIsCorrect(): ?bool
    {
        return $this->isCorrect;
    }

    public function setIsCorrect(?bool $isCorrect): static
    {
        $this->isCorrect = $isCorrect;

        return $this;
    }

    /**
     * @throws Throwable
     */
    public function calculateCorrectness(): bool
    {
        if ($this->isCorrect !== null) {
            throw new Exception('Correctness was already calculated.');
        }

        $question = $this->getQuestion();
        if ($question->getType() !== QuestionType::MULTI_SELECT) {
            throw new Exception('Not implemented yet');
        }

        $answerOptionsMap = [];
        foreach ($question->getAnswerOptions() as $answerOption) {
            $answerOptionsMap[$answerOption->getId()] = $answerOption;
        }

        foreach ($this->getAnswer() as $selectedOptionId) {
            $answerOption = $answerOptionsMap[$selectedOptionId];
            if (!$answerOption->isCorrect()) {
                $this->isCorrect = false;
                return false;
            }
        }

        $this->isCorrect = true;

        return true;
    }
}
