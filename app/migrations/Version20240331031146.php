<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240331031146 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE guest_booking DROP FOREIGN KEY FK_A0C929923301C60');
        $this->addSql('ALTER TABLE guest_booking DROP FOREIGN KEY FK_A0C929929A4AA658');
        $this->addSql('DROP TABLE guest_booking');
        $this->addSql('ALTER TABLE guests ADD booking_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE guests ADD CONSTRAINT FK_4D11BCB23301C60 FOREIGN KEY (booking_id) REFERENCES bookings (id)');
        $this->addSql('CREATE INDEX IDX_4D11BCB23301C60 ON guests (booking_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE guest_booking (guest_id INT NOT NULL, booking_id INT NOT NULL, INDEX IDX_A0C929929A4AA658 (guest_id), INDEX IDX_A0C929923301C60 (booking_id), PRIMARY KEY(guest_id, booking_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE guest_booking ADD CONSTRAINT FK_A0C929923301C60 FOREIGN KEY (booking_id) REFERENCES bookings (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE guest_booking ADD CONSTRAINT FK_A0C929929A4AA658 FOREIGN KEY (guest_id) REFERENCES guests (id)');
        $this->addSql('ALTER TABLE guests DROP FOREIGN KEY FK_4D11BCB23301C60');
        $this->addSql('DROP INDEX IDX_4D11BCB23301C60 ON guests');
        $this->addSql('ALTER TABLE guests DROP booking_id');
    }
}
