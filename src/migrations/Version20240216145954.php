<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Enum\QuestionType;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240216145954 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Setting up a default questionnaire from the assignment
        $this->addSql(
            'INSERT INTO questionnaires (id, title) VALUES (?, ?)',
            [1, 'Simple test'],
        );

        $questions = [
            [
                '1 + 1 =',
                [['3', false], ['2', true], ['0', false]],
            ],
            [
                '2 + 2 =',
                [['4', true], ['3 + 1', true], ['10', false]],
            ],
            [
                '3 + 3 =',
                [['1 + 5', true], ['1', false], ['6', true], ['2 + 4', true]],
            ],
            [
                '4 + 4 =',
                [['8', true], ['4', false], ['0', false], ['0 + 8', true]],
            ],
            [
                '5 + 5 =',
                [['6', false], ['18', false], ['10', true], ['9', false], ['0', false]],
            ],
            [
                '6 + 6 =',
                [['3', false], ['9', false], ['0', false], ['12', true], ['5 + 7', true]],
            ],
            [
                '7 + 7 =',
                [['5', false], ['14', true]],
            ],
            [
                '8 + 8 =',
                [['16', true], ['12', false], ['9', false], ['5', false]],
            ],
            [
                '9 + 9 =',
                [['18', true], ['9', false], ['17 + 1', true], ['2 + 16', true]],
            ],
            [
                '10 + 10 =',
                [['0', false], ['2', false], ['8', false], ['20', true]],
            ],
        ];

        foreach ($questions as $i => $question) {
            [$title, $answers] = $question;
            $this->addSql(
                'INSERT INTO questions (id, questionnaire_id, title, type, created_at) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)',
                [$i + 1, 1, $title, QuestionType::MULTI_SELECT->value],
            );

            foreach ($answers as $answer) {
                [$text, $isCorrect] = $answer;
                $this->addSql(
                    'INSERT INTO answer_options (id, question_id, text, is_correct) VALUES (nextval(\'answer_options_id_seq\'), ?, ?, ?)',
                    [$i + 1, $text, (int) $isCorrect],
                );
            }
        }
    }

    public function down(Schema $schema): void
    {

    }
}
