<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170417203026 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B71AD5CDBF');
        $this->addSql('DROP INDEX UNIQ_BA388B71AD5CDBF ON cart');
        $this->addSql('ALTER TABLE cart CHANGE cart_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BA388B7A76ED395 ON cart (user_id)');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9A76ED395');
        $this->addSql('DROP INDEX UNIQ_1483A5E9A76ED395 ON users');
        $this->addSql('ALTER TABLE users CHANGE user_id cart_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E91AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E91AD5CDBF ON users (cart_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B7A76ED395');
        $this->addSql('DROP INDEX UNIQ_BA388B7A76ED395 ON cart');
        $this->addSql('ALTER TABLE cart CHANGE user_id cart_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B71AD5CDBF FOREIGN KEY (cart_id) REFERENCES users (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BA388B71AD5CDBF ON cart (cart_id)');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E91AD5CDBF');
        $this->addSql('DROP INDEX UNIQ_1483A5E91AD5CDBF ON users');
        $this->addSql('ALTER TABLE users CHANGE cart_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9A76ED395 FOREIGN KEY (user_id) REFERENCES cart (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9A76ED395 ON users (user_id)');
    }
}
