<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\QuestionnaireRepository;
use App\Service\QuestionnaireAttemptManager;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class QuestionnaireController extends AbstractController
{
    #[Route('/', name: 'questionnaires')]
    public function index(QuestionnaireRepository $repository): Response
    {
        $questionnaires = $repository->findAll();

        return $this->render('questionnaire/list.html.twig', compact('questionnaires'));
    }

    /**
     * @throws Exception
     */
    #[Route('/questionnaires/{id}/attempt', name: 'questionnaires_attempt', methods: ['POST'])]
    public function attempt(
        int $id,
        QuestionnaireRepository $repository,
        QuestionnaireAttemptManager $attemptManager,
    ): Response {
        $questionnaire = $repository->find($id);
        if (!$questionnaire) {
            throw $this->createNotFoundException('The questionnaire does not exist.');
        }

        $attempt = $attemptManager->makeAttempt($questionnaire);

        return $this->redirectToRoute('questionnaire_attempt_show', ['id' => $attempt->getId()]);
    }
}
