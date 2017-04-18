<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170417202804 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE cart (id INT AUTO_INCREMENT NOT NULL, cart_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_BA388B71AD5CDBF (cart_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE carts_products (cart_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_12E5DBFB1AD5CDBF (cart_id), INDEX IDX_12E5DBFB4584665A (product_id), PRIMARY KEY(cart_id, product_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B71AD5CDBF FOREIGN KEY (cart_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE carts_products ADD CONSTRAINT FK_12E5DBFB1AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id)');
        $this->addSql('ALTER TABLE carts_products ADD CONSTRAINT FK_12E5DBFB4584665A FOREIGN KEY (product_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE users ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9A76ED395 FOREIGN KEY (user_id) REFERENCES cart (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9A76ED395 ON users (user_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE carts_products DROP FOREIGN KEY FK_12E5DBFB1AD5CDBF');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9A76ED395');
        $this->addSql('DROP TABLE cart');
        $this->addSql('DROP TABLE carts_products');
        $this->addSql('DROP INDEX UNIQ_1483A5E9A76ED395 ON users');
        $this->addSql('ALTER TABLE users DROP user_id');
    }
}
