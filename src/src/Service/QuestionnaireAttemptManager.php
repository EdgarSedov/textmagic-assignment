<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\{Questionnaire, QuestionnaireAttempt, UserAnswer};
use App\Repository\QuestionnaireAttemptRepository;
use App\Service\Dto\SubmittedAnswerDto;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

readonly class QuestionnaireAttemptManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private QuestionnaireAttemptRepository $attemptRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws Exception
     */
    public function makeAttempt(Questionnaire $questionnaire): QuestionnaireAttempt
    {
        try {
            $attempt = new QuestionnaireAttempt();
            $attempt->setQuestionnaire($questionnaire);

            $questions = $questionnaire->getQuestions()->toArray();
            shuffle($questions);

            $seqNumber = 1;
            foreach ($questions as $question) {
                $userAnswer = new UserAnswer();
                $userAnswer->setQuestionnaireAttempt($attempt);
                $userAnswer->setQuestion($question);
                $userAnswer->setSequenceNumber($seqNumber++);

                $attempt->addUserAnswer($userAnswer);
                $this->entityManager->persist($userAnswer);
            }

            $this->entityManager->persist($attempt);
            $this->entityManager->flush();
        } catch (Throwable $exception) {
            $this->logger->error('Cannot make new questionnaire attempt', [
                'questionnaireId' => $questionnaire->getId(),
                'exception' => $exception,
            ]);

            throw new Exception('Cannot make new attempt', 0, $exception);
        }

        return $attempt;
    }

    /**
     * @param array<SubmittedAnswerDto> $submittedAnswerDtos
     * @throws Throwable
     */
    public function submitAttemptAnswers(int $attemptId, array $submittedAnswerDtos): QuestionnaireAttempt
    {
        try {
            $attempt = $this->attemptRepository->findWithDetails($attemptId);
            if (!$attempt) {
                throw new NotFoundHttpException('Attempt does not exist.');
            }

            $userAnswersMap = [];
            foreach ($attempt->getUserAnswers() as $userAnswer) {
                $userAnswersMap[$userAnswer->getId()] = $userAnswer;
            }

            foreach ($submittedAnswerDtos as $submittedAnswerDto) {
                $userAnswer = $userAnswersMap[$submittedAnswerDto->userAnswerId];
                $userAnswer->setAnswer($submittedAnswerDto->answer);
                $userAnswer->setAnsweredAt(new DateTimeImmutable());
            }

            if ($attempt->hasAllAnswersSubmitted()) {
                $this->completeAttempt($attempt);
            }

            $this->entityManager->flush();

            return $attempt;
        } catch (NotFoundHttpException $e) {
            throw $e;
        } catch (Throwable $e) {
            $this->logger->error('Cannot submit answers for attempt', [
                'attemptId' => $attempt->getId(),
                'submittedAnswers' => $submittedAnswerDtos,
                'exception' => $e,
            ]);

            throw new Exception('Cannot submit answers for attempt', 0, $e);
        }
    }

    /**
     * @throws Throwable
     */
    protected function completeAttempt(QuestionnaireAttempt $attempt): void
    {
        $attempt->setCompletedAt(new DateTimeImmutable());
        foreach ($attempt->getUserAnswers() as $userAnswer) {
            $userAnswer->calculateCorrectness();
        }
    }
}
