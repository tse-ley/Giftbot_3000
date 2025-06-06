<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250606083324 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE cart_item (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, gift_id INTEGER DEFAULT NULL, quantity INTEGER NOT NULL, CONSTRAINT FK_F0FE2527A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F0FE252797A95A83 FOREIGN KEY (gift_id) REFERENCES gift (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F0FE2527A76ED395 ON cart_item (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F0FE252797A95A83 ON cart_item (gift_id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE cart_items
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE cart_items (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, gift_id INTEGER DEFAULT NULL, quantity INTEGER NOT NULL, CONSTRAINT FK_BEF48445A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_BEF4844597A95A83 FOREIGN KEY (gift_id) REFERENCES gift (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_BEF4844597A95A83 ON cart_items (gift_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_BEF48445A76ED395 ON cart_items (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE cart_item
        SQL);
    }
}
