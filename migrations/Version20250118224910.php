<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250118224910 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'init database';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE currency (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(20) NOT NULL, value DOUBLE PRECISION NOT NULL, sid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', created DATETIME NOT NULL, updated DATETIME NOT NULL, deleted DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_6956883F77153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, status_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, price DOUBLE PRECISION NOT NULL, sid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', created DATETIME NOT NULL, updated DATETIME NOT NULL, deleted DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_D34A04AD5E237E06 (name), INDEX IDX_D34A04AD6BF700BD (status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, sid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', created DATETIME NOT NULL, updated DATETIME NOT NULL, deleted DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_CDFC73565E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_category_product (product_category_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_9A1E202FBE6903FD (product_category_id), INDEX IDX_9A1E202F4584665A (product_id), PRIMARY KEY(product_category_id, product_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_status (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, sid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', created DATETIME NOT NULL, updated DATETIME NOT NULL, deleted DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_197C24B85E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, sid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', created DATETIME NOT NULL, updated DATETIME NOT NULL, deleted DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD6BF700BD FOREIGN KEY (status_id) REFERENCES product_status (id)');
        $this->addSql('ALTER TABLE product_category_product ADD CONSTRAINT FK_9A1E202FBE6903FD FOREIGN KEY (product_category_id) REFERENCES product_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_category_product ADD CONSTRAINT FK_9A1E202F4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD6BF700BD');
        $this->addSql('ALTER TABLE product_category_product DROP FOREIGN KEY FK_9A1E202FBE6903FD');
        $this->addSql('ALTER TABLE product_category_product DROP FOREIGN KEY FK_9A1E202F4584665A');
        $this->addSql('DROP TABLE currency');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_category');
        $this->addSql('DROP TABLE product_category_product');
        $this->addSql('DROP TABLE product_status');
        $this->addSql('DROP TABLE `user`');
    }
}
