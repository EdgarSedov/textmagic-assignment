<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\QuestionnaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionnaireRepository::class)]
#[ORM\Table(name: "questionnaires")]
class Questionnaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\OneToMany(targetEntity: Question::class, mappedBy: 'questionnaire', orphanRemoval: true)]
    private Collection $questions;

    #[ORM\OneToMany(targetEntity: QuestionnaireAttempt::class, mappedBy: 'quiestionnaire', orphanRemoval: true)]
    private Collection $attempts;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->attempts = new ArrayCollection();
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

    /**
     * @return Collection<int, Question>
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): static
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
            $question->setQuestionnaire($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): static
    {
        if ($this->questions->removeElement($question)) {
            // set the owning side to null (unless already changed)
            if ($question->getQuestionnaire() === $this) {
                $question->setQuestionnaire(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, QuestionnaireAttempt>
     */
    public function getAttempts(): Collection
    {
        return $this->attempts;
    }

    public function addAttempt(QuestionnaireAttempt $attempt): static
    {
        if (!$this->attempts->contains($attempt)) {
            $this->attempts->add($attempt);
            $attempt->setQuestionnaire($this);
        }

        return $this;
    }

    public function removeAttempt(QuestionnaireAttempt $attempt): static
    {
        if ($this->attempts->removeElement($attempt)) {
            // set the owning side to null (unless already changed)
            if ($attempt->getQuestionnaire() === $this) {
                $attempt->setQuestionnaire(null);
            }
        }

        return $this;
    }
}
