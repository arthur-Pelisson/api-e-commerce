<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210428093112 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_items MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE order_items DROP FOREIGN KEY order_items_ibfk_2');
        $this->addSql('DROP INDEX order_id ON order_items');
        $this->addSql('ALTER TABLE order_items DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE order_items DROP id, CHANGE product_id product_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_62809DB08D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE order_items ADD PRIMARY KEY (order_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_items DROP FOREIGN KEY FK_62809DB08D9F6D38');
        $this->addSql('ALTER TABLE order_items ADD id INT AUTO_INCREMENT NOT NULL, CHANGE product_id product_id INT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT order_items_ibfk_2 FOREIGN KEY (order_id) REFERENCES orders (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('CREATE INDEX order_id ON order_items (order_id)');
    }
}
