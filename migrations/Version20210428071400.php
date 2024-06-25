<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210428071400 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_items DROP FOREIGN KEY order_items_ibfk_1');
        $this->addSql('ALTER TABLE order_items DROP FOREIGN KEY order_items_ibfk_2');
        $this->addSql('DROP INDEX product_id ON order_items');
        $this->addSql('ALTER TABLE order_items ADD id INT AUTO_INCREMENT NOT NULL, ADD order_id_id INT NOT NULL, ADD product_id_id INT NOT NULL, DROP order_id, DROP product_id, DROP quantity, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_62809DB0FCDAEAAA FOREIGN KEY (order_id_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_62809DB0DE18E50B FOREIGN KEY (product_id_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_62809DB0FCDAEAAA ON order_items (order_id_id)');
        $this->addSql('CREATE INDEX IDX_62809DB0DE18E50B ON order_items (product_id_id)');
        $this->addSql('DROP INDEX products ON orders');
        $this->addSql('ALTER TABLE orders ADD product_id INT NOT NULL, ADD item_order_id INT NOT NULL, CHANGE products products LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE product ADD order_items_id INT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_items MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE order_items DROP FOREIGN KEY FK_62809DB0FCDAEAAA');
        $this->addSql('ALTER TABLE order_items DROP FOREIGN KEY FK_62809DB0DE18E50B');
        $this->addSql('DROP INDEX IDX_62809DB0FCDAEAAA ON order_items');
        $this->addSql('DROP INDEX IDX_62809DB0DE18E50B ON order_items');
        $this->addSql('ALTER TABLE order_items DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE order_items ADD order_id INT NOT NULL, ADD product_id INT NOT NULL, ADD quantity INT NOT NULL, DROP id, DROP order_id_id, DROP product_id_id');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT order_items_ibfk_1 FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT order_items_ibfk_2 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('CREATE INDEX product_id ON order_items (product_id)');
        $this->addSql('ALTER TABLE order_items ADD PRIMARY KEY (order_id)');
        $this->addSql('ALTER TABLE orders DROP product_id, DROP item_order_id, CHANGE products products INT NOT NULL');
        $this->addSql('CREATE INDEX products ON orders (products)');
        $this->addSql('ALTER TABLE product DROP order_items_id');
    }
}
