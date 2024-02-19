<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\QuestionnaireAttemptRepository;
use App\Service\Dto\SubmittedAnswerDto;
use App\Service\QuestionnaireAttemptManager;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

class QuestionnaireAttemptController extends AbstractController
{
    #[Route('/questionnaire-attempt/{id}', name: 'questionnaire_attempt_show', methods: ['GET'])]
    public function show(int $id, QuestionnaireAttemptRepository $repository): Response
    {
        $attempt = $repository->findWithDetails($id);
        if (!$attempt) {
            throw $this->createNotFoundException('The attempt does not exist.');
        }

        if ($attempt->getCompletedAt() !== null) {
            return $this->render('questionnaire_attempt/result.html.twig', compact('attempt'));
        }

        foreach ($attempt->getUserAnswers() as $userAnswer) {
            $question = $userAnswer->getQuestion();

            $answerOptions = $question->getAnswerOptions()->toArray();
            shuffle($answerOptions);

            $question->setAnswerOptions(new ArrayCollection($answerOptions));
        }

        return $this->render('questionnaire_attempt/show.html.twig', compact('attempt'));
    }

    /**
     * @throws Throwable
     */
    #[Route('/questionnaire-attempt/{id}', name: 'questionnaire_attempt_submit', methods: ['POST'])]
    public function submit(Request $request, int $id, QuestionnaireAttemptManager $attemptManager): Response
    {
        // Retrieve the answers from the form
        $submittedAnswers = $request->request->all('answers');

        // Form array of dtos with answers
        $userAnswerDtos = [];
        foreach ($submittedAnswers as $userAnswerId => $answer) {
            $userAnswerDtos[] = new SubmittedAnswerDto(
                (int) $userAnswerId,
                $answer,
            );
        }

        $attempt = $attemptManager->submitAttemptAnswers($id, $userAnswerDtos);
        if ($attempt->getCompletedAt() !== null) {
            return $this->render('questionnaire_attempt/result.html.twig', compact('attempt'));
        }

        return $this->render('questionnaire_attempt/show.html.twig', compact('attempt'));
    }
}
