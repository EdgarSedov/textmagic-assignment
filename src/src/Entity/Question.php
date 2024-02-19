<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\QuestionType;
use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
#[ORM\Table(name: "questions")]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?QuestionType $type = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'questions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Questionnaire $questionnaire = null;

    #[ORM\OneToMany(targetEntity: AnswerOption::class, mappedBy: 'question', orphanRemoval: true)]
    private Collection $answerOptions;

    public function __construct()
    {
        $this->answerOptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getType(): ?QuestionType
    {
        return $this->type;
    }

    public function setType(QuestionType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getQuestionnaire(): ?Questionnaire
    {
        return $this->questionnaire;
    }

    public function setQuestionnaire(?Questionnaire $questionnaire): static
    {
        $this->questionnaire = $questionnaire;

        return $this;
    }

    /**
     * @return Collection<int, AnswerOption>
     */
    public function getAnswerOptions(): Collection
    {
        return $this->answerOptions;
    }

    /**
     * @param Collection<int, AnswerOption> $answerOptions
     */
    public function setAnswerOptions(Collection $answerOptions)
    {
        $this->answerOptions = $answerOptions;

        return $this;
    }

    public function addAnswerOption(AnswerOption $answerOption): static
    {
        if (!$this->answerOptions->contains($answerOption)) {
            $this->answerOptions->add($answerOption);
            $answerOption->setQuestion($this);
        }

        return $this;
    }

    public function removeAnswerOption(AnswerOption $answerOption): static
    {
        if ($this->answerOptions->removeElement($answerOption)) {
            // set the owning side to null (unless already changed)
            if ($answerOption->getQuestion() === $this) {
                $answerOption->setQuestion(null);
            }
        }

        return $this;
    }
}
