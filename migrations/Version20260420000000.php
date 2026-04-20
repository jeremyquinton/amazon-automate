<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260420000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename listing.quantity column to amazon_warehouse_stock';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE listing CHANGE quantity amazon_warehouse_stock INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE listing CHANGE amazon_warehouse_stock quantity INT DEFAULT NULL');
    }
}
