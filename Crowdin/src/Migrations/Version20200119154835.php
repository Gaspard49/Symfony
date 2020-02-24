<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200119154835 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE traduction_source (id INT AUTO_INCREMENT NOT NULL, project_id INT NOT NULL, source VARCHAR(255) NOT NULL, INDEX IDX_E51D494166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE language (id INT AUTO_INCREMENT NOT NULL, language_name INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, pseudo VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, reset_token VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, language LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D64986CC499D (pseudo), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_language (user_id INT NOT NULL, language_id INT NOT NULL, INDEX IDX_345695B5A76ED395 (user_id), INDEX IDX_345695B582F1BAF4 (language_id), PRIMARY KEY(user_id, language_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE traduction_target (id INT AUTO_INCREMENT NOT NULL, language_id INT NOT NULL, traduction_source_id INT NOT NULL, target VARCHAR(255) NOT NULL, INDEX IDX_17B4841B82F1BAF4 (language_id), INDEX IDX_17B4841B6563B46B (traduction_source_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, language_id INT NOT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, file VARCHAR(255) DEFAULT NULL, INDEX IDX_2FB3D0EEA76ED395 (user_id), INDEX IDX_2FB3D0EE82F1BAF4 (language_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE traduction_source ADD CONSTRAINT FK_E51D494166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE user_language ADD CONSTRAINT FK_345695B5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_language ADD CONSTRAINT FK_345695B582F1BAF4 FOREIGN KEY (language_id) REFERENCES language (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE traduction_target ADD CONSTRAINT FK_17B4841B82F1BAF4 FOREIGN KEY (language_id) REFERENCES language (id)');
        $this->addSql('ALTER TABLE traduction_target ADD CONSTRAINT FK_17B4841B6563B46B FOREIGN KEY (traduction_source_id) REFERENCES traduction_source (id)');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE82F1BAF4 FOREIGN KEY (language_id) REFERENCES language (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE traduction_target DROP FOREIGN KEY FK_17B4841B6563B46B');
        $this->addSql('ALTER TABLE user_language DROP FOREIGN KEY FK_345695B582F1BAF4');
        $this->addSql('ALTER TABLE traduction_target DROP FOREIGN KEY FK_17B4841B82F1BAF4');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE82F1BAF4');
        $this->addSql('ALTER TABLE user_language DROP FOREIGN KEY FK_345695B5A76ED395');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EEA76ED395');
        $this->addSql('ALTER TABLE traduction_source DROP FOREIGN KEY FK_E51D494166D1F9C');
        $this->addSql('DROP TABLE traduction_source');
        $this->addSql('DROP TABLE language');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_language');
        $this->addSql('DROP TABLE traduction_target');
        $this->addSql('DROP TABLE project');
    }
}
