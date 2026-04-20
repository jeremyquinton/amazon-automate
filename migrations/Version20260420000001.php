<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260420000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create restock_items and manifest_files tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE restock_items (
            id INT AUTO_INCREMENT NOT NULL,
            title VARCHAR(200) DEFAULT NULL,
            sku VARCHAR(55) DEFAULT NULL,
            my_soh INT DEFAULT NULL,
            total_sales_last_30_days INT DEFAULT NULL,
            total_stock_days_cover INT DEFAULT NULL,
            items_to_send INT DEFAULT NULL,
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE manifest_files (
            id INT AUTO_INCREMENT NOT NULL,
            filename VARCHAR(255) NOT NULL,
            filepath VARCHAR(500) NOT NULL,
            created_at DATETIME NOT NULL,
            item_count INT DEFAULT NULL,
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE restock_items');
        $this->addSql('DROP TABLE manifest_files');
    }
}
