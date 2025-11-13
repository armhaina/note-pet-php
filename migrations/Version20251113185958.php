<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251113185958 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "notes" (id SERIAL NOT NULL, user_id INT NOT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_11BA68CA76ED395 ON "notes" (user_id)');
        $this->addSql('COMMENT ON COLUMN "notes".user_id IS \'ID пользователя\'');
        $this->addSql('COMMENT ON COLUMN "notes".name IS \'Наименование\'');
        $this->addSql('COMMENT ON COLUMN "notes".description IS \'Текст\'');
        $this->addSql('COMMENT ON COLUMN "notes".created_at IS \'Дата создания(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "notes".updated_at IS \'Дата изменения(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "users" (id SERIAL NOT NULL, roles JSON NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON "users" (email)');
        $this->addSql('COMMENT ON COLUMN "users".roles IS \'Роли пользователя\'');
        $this->addSql('COMMENT ON COLUMN "users".email IS \'Электронная почта пользователя\'');
        $this->addSql('COMMENT ON COLUMN "users".password IS \'Пароль пользователя\'');
        $this->addSql('COMMENT ON COLUMN "users".created_at IS \'Дата создания(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "users".updated_at IS \'Дата изменения(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE "notes" ADD CONSTRAINT FK_11BA68CA76ED395 FOREIGN KEY (user_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "notes" DROP CONSTRAINT FK_11BA68CA76ED395');
        $this->addSql('DROP TABLE "notes"');
        $this->addSql('DROP TABLE "users"');
    }
}
