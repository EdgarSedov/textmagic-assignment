<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240216143041 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE answer_options_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE questionnaire_attempts_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE questionnaires_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE questions_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_answers_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE answer_options (id BIGINT NOT NULL, question_id INT NOT NULL, text TEXT NOT NULL, is_correct BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_987535F61E27F6BF ON answer_options (question_id)');
        $this->addSql('CREATE TABLE questionnaire_attempts (id INT NOT NULL, questionnaire_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, completed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_21FCEEE9DDE72D0B ON questionnaire_attempts (questionnaire_id)');
        $this->addSql('COMMENT ON COLUMN questionnaire_attempts.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN questionnaire_attempts.completed_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE questionnaires (id INT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE questions (id INT NOT NULL, questionnaire_id INT NOT NULL, title VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8ADC54D5CE07E8FF ON questions (questionnaire_id)');
        $this->addSql('COMMENT ON COLUMN questions.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE user_answers (id BIGINT NOT NULL, questionnaire_attempt_id INT NOT NULL, question_id INT NOT NULL, sequence_number SMALLINT NOT NULL, answer TEXT DEFAULT NULL, answered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_correct BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8DDD80C46607EF2 ON user_answers (questionnaire_attempt_id)');
        $this->addSql('CREATE INDEX IDX_8DDD80C1E27F6BF ON user_answers (question_id)');
        $this->addSql('COMMENT ON COLUMN user_answers.answered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE answer_options ADD CONSTRAINT FK_987535F61E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE questionnaire_attempts ADD CONSTRAINT FK_21FCEEE9DDE72D0B FOREIGN KEY (questionnaire_id) REFERENCES questionnaires (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE questions ADD CONSTRAINT FK_8ADC54D5CE07E8FF FOREIGN KEY (questionnaire_id) REFERENCES questionnaires (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_answers ADD CONSTRAINT FK_8DDD80C46607EF2 FOREIGN KEY (questionnaire_attempt_id) REFERENCES questionnaire_attempts (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_answers ADD CONSTRAINT FK_8DDD80C1E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE answer_options_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE questionnaire_attempts_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE questionnaires_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE questions_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE user_answers_id_seq CASCADE');
        $this->addSql('ALTER TABLE answer_options DROP CONSTRAINT FK_987535F61E27F6BF');
        $this->addSql('ALTER TABLE questionnaire_attempts DROP CONSTRAINT FK_21FCEEE9DDE72D0B');
        $this->addSql('ALTER TABLE questions DROP CONSTRAINT FK_8ADC54D5CE07E8FF');
        $this->addSql('ALTER TABLE user_answers DROP CONSTRAINT FK_8DDD80C46607EF2');
        $this->addSql('ALTER TABLE user_answers DROP CONSTRAINT FK_8DDD80C1E27F6BF');
        $this->addSql('DROP TABLE answer_options');
        $this->addSql('DROP TABLE questionnaire_attempts');
        $this->addSql('DROP TABLE questionnaires');
        $this->addSql('DROP TABLE questions');
        $this->addSql('DROP TABLE user_answers');
    }
}
