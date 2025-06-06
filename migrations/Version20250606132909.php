<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250606132909 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE `admin` (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, UNIQUE INDEX UNIQ_880E0D76F85E0677 (username), UNIQUE INDEX UNIQ_880E0D76E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE cart_item (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, gift_id INT DEFAULT NULL, quantity INT NOT NULL, INDEX IDX_F0FE2527A76ED395 (user_id), INDEX IDX_F0FE252797A95A83 (gift_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE gift (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, price NUMERIC(10, 0) NOT NULL, category VARCHAR(255) NOT NULL, stock_quantity INT NOT NULL, image_url VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE news_letter (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, subscribed_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE order_items (id INT AUTO_INCREMENT NOT NULL, orders_id INT DEFAULT NULL, gift_id INT DEFAULT NULL, quantity INT NOT NULL, price NUMERIC(10, 0) NOT NULL, INDEX IDX_62809DB0CFFE9AD6 (orders_id), INDEX IDX_62809DB097A95A83 (gift_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE orders (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, total_price NUMERIC(10, 0) NOT NULL, is_available TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_E52FFDEEA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_admin TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE wish_list (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, gift_id INT DEFAULT NULL, added_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', is_public TINYINT(1) NOT NULL, viewed_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', shared_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_5B8739BDA76ED395 (user_id), INDEX IDX_5B8739BD97A95A83 (gift_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE wish_list_item (id INT AUTO_INCREMENT NOT NULL, wishlist_id INT DEFAULT NULL, gift_id INT DEFAULT NULL, user_id INT DEFAULT NULL, added_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_9A7FA711FB8E54CD (wishlist_id), INDEX IDX_9A7FA71197A95A83 (gift_id), INDEX IDX_9A7FA711A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cart_item ADD CONSTRAINT FK_F0FE2527A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cart_item ADD CONSTRAINT FK_F0FE252797A95A83 FOREIGN KEY (gift_id) REFERENCES gift (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE order_items ADD CONSTRAINT FK_62809DB0CFFE9AD6 FOREIGN KEY (orders_id) REFERENCES orders (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE order_items ADD CONSTRAINT FK_62809DB097A95A83 FOREIGN KEY (gift_id) REFERENCES gift (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE wish_list ADD CONSTRAINT FK_5B8739BDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE wish_list ADD CONSTRAINT FK_5B8739BD97A95A83 FOREIGN KEY (gift_id) REFERENCES gift (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE wish_list_item ADD CONSTRAINT FK_9A7FA711FB8E54CD FOREIGN KEY (wishlist_id) REFERENCES wish_list (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE wish_list_item ADD CONSTRAINT FK_9A7FA71197A95A83 FOREIGN KEY (gift_id) REFERENCES gift (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE wish_list_item ADD CONSTRAINT FK_9A7FA711A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE cart_item DROP FOREIGN KEY FK_F0FE2527A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cart_item DROP FOREIGN KEY FK_F0FE252797A95A83
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE order_items DROP FOREIGN KEY FK_62809DB0CFFE9AD6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE order_items DROP FOREIGN KEY FK_62809DB097A95A83
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEEA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE wish_list DROP FOREIGN KEY FK_5B8739BDA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE wish_list DROP FOREIGN KEY FK_5B8739BD97A95A83
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE wish_list_item DROP FOREIGN KEY FK_9A7FA711FB8E54CD
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE wish_list_item DROP FOREIGN KEY FK_9A7FA71197A95A83
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE wish_list_item DROP FOREIGN KEY FK_9A7FA711A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE `admin`
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE cart_item
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE gift
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE news_letter
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE order_items
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE orders
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE wish_list
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE wish_list_item
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
