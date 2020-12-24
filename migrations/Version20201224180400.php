<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201224180400 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE brand (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(127) NOT NULL, manufacturer VARCHAR(127) NOT NULL, serie VARCHAR(31) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE buyer (id INT AUTO_INCREMENT NOT NULL, store_account_id INT NOT NULL, first_name VARCHAR(127) NOT NULL, last_name VARCHAR(127) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_84905FB3B5820457 (store_account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE color (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(127) DEFAULT NULL, hexadecimal VARCHAR(6) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dimensions (id INT AUTO_INCREMENT NOT NULL, width INT NOT NULL, height INT NOT NULL, unit VARCHAR(2) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE display (id INT AUTO_INCREMENT NOT NULL, pixel_size_id INT NOT NULL, viewport_id INT NOT NULL, touchscreen TINYINT(1) NOT NULL, INDEX IDX_CD172A391E4F49E (pixel_size_id), INDEX IDX_CD172A3B06AF66 (viewport_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE os (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(127) NOT NULL, manufacturer VARCHAR(127) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE phone (id INT AUTO_INCREMENT NOT NULL, display_id INT NOT NULL, brand_id INT NOT NULL, processor_id INT NOT NULL, size_id INT NOT NULL, weight SMALLINT NOT NULL, INDEX IDX_444F97DD51A2DF33 (display_id), UNIQUE INDEX UNIQ_444F97DD44F5D008 (brand_id), INDEX IDX_444F97DD37BAC19A (processor_id), INDEX IDX_444F97DD498DA827 (size_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE phone_color (phone_id INT NOT NULL, color_id INT NOT NULL, INDEX IDX_950C3DF33B7323CB (phone_id), INDEX IDX_950C3DF37ADA1FB5 (color_id), PRIMARY KEY(phone_id, color_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE phone_storage (phone_id INT NOT NULL, storage_id INT NOT NULL, INDEX IDX_C43897063B7323CB (phone_id), INDEX IDX_C43897065CC5DB90 (storage_id), PRIMARY KEY(phone_id, storage_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE phone_os (phone_id INT NOT NULL, os_id INT NOT NULL, INDEX IDX_EEFA018A3B7323CB (phone_id), INDEX IDX_EEFA018A3DCA04D1 (os_id), PRIMARY KEY(phone_id, os_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE processor (id INT AUTO_INCREMENT NOT NULL, brand_id INT NOT NULL, cores SMALLINT NOT NULL, frequency INT NOT NULL, UNIQUE INDEX UNIQ_29C0465044F5D008 (brand_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, phone_id INT NOT NULL, price NUMERIC(6, 2) NOT NULL, description VARCHAR(255) DEFAULT NULL, launch_date DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_D34A04AD3B7323CB (phone_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE storage (id INT AUTO_INCREMENT NOT NULL, capacity INT NOT NULL, unit VARCHAR(2) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE store_account (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(127) NOT NULL, email VARCHAR(127) NOT NULL, password VARCHAR(31) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE buyer ADD CONSTRAINT FK_84905FB3B5820457 FOREIGN KEY (store_account_id) REFERENCES store_account (id)');
        $this->addSql('ALTER TABLE display ADD CONSTRAINT FK_CD172A391E4F49E FOREIGN KEY (pixel_size_id) REFERENCES dimensions (id)');
        $this->addSql('ALTER TABLE display ADD CONSTRAINT FK_CD172A3B06AF66 FOREIGN KEY (viewport_id) REFERENCES dimensions (id)');
        $this->addSql('ALTER TABLE phone ADD CONSTRAINT FK_444F97DD51A2DF33 FOREIGN KEY (display_id) REFERENCES display (id)');
        $this->addSql('ALTER TABLE phone ADD CONSTRAINT FK_444F97DD44F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id)');
        $this->addSql('ALTER TABLE phone ADD CONSTRAINT FK_444F97DD37BAC19A FOREIGN KEY (processor_id) REFERENCES processor (id)');
        $this->addSql('ALTER TABLE phone ADD CONSTRAINT FK_444F97DD498DA827 FOREIGN KEY (size_id) REFERENCES dimensions (id)');
        $this->addSql('ALTER TABLE phone_color ADD CONSTRAINT FK_950C3DF33B7323CB FOREIGN KEY (phone_id) REFERENCES phone (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE phone_color ADD CONSTRAINT FK_950C3DF37ADA1FB5 FOREIGN KEY (color_id) REFERENCES color (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE phone_storage ADD CONSTRAINT FK_C43897063B7323CB FOREIGN KEY (phone_id) REFERENCES phone (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE phone_storage ADD CONSTRAINT FK_C43897065CC5DB90 FOREIGN KEY (storage_id) REFERENCES storage (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE phone_os ADD CONSTRAINT FK_EEFA018A3B7323CB FOREIGN KEY (phone_id) REFERENCES phone (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE phone_os ADD CONSTRAINT FK_EEFA018A3DCA04D1 FOREIGN KEY (os_id) REFERENCES os (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE processor ADD CONSTRAINT FK_29C0465044F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD3B7323CB FOREIGN KEY (phone_id) REFERENCES phone (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE phone DROP FOREIGN KEY FK_444F97DD44F5D008');
        $this->addSql('ALTER TABLE processor DROP FOREIGN KEY FK_29C0465044F5D008');
        $this->addSql('ALTER TABLE phone_color DROP FOREIGN KEY FK_950C3DF37ADA1FB5');
        $this->addSql('ALTER TABLE display DROP FOREIGN KEY FK_CD172A391E4F49E');
        $this->addSql('ALTER TABLE display DROP FOREIGN KEY FK_CD172A3B06AF66');
        $this->addSql('ALTER TABLE phone DROP FOREIGN KEY FK_444F97DD498DA827');
        $this->addSql('ALTER TABLE phone DROP FOREIGN KEY FK_444F97DD51A2DF33');
        $this->addSql('ALTER TABLE phone_os DROP FOREIGN KEY FK_EEFA018A3DCA04D1');
        $this->addSql('ALTER TABLE phone_color DROP FOREIGN KEY FK_950C3DF33B7323CB');
        $this->addSql('ALTER TABLE phone_storage DROP FOREIGN KEY FK_C43897063B7323CB');
        $this->addSql('ALTER TABLE phone_os DROP FOREIGN KEY FK_EEFA018A3B7323CB');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD3B7323CB');
        $this->addSql('ALTER TABLE phone DROP FOREIGN KEY FK_444F97DD37BAC19A');
        $this->addSql('ALTER TABLE phone_storage DROP FOREIGN KEY FK_C43897065CC5DB90');
        $this->addSql('ALTER TABLE buyer DROP FOREIGN KEY FK_84905FB3B5820457');
        $this->addSql('DROP TABLE brand');
        $this->addSql('DROP TABLE buyer');
        $this->addSql('DROP TABLE color');
        $this->addSql('DROP TABLE dimensions');
        $this->addSql('DROP TABLE display');
        $this->addSql('DROP TABLE os');
        $this->addSql('DROP TABLE phone');
        $this->addSql('DROP TABLE phone_color');
        $this->addSql('DROP TABLE phone_storage');
        $this->addSql('DROP TABLE phone_os');
        $this->addSql('DROP TABLE processor');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE storage');
        $this->addSql('DROP TABLE store_account');
    }
}
