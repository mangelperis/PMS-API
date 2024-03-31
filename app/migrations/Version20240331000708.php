<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240331000708 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE INDEX hotel_id ON bookings (hotel_id)');
        $this->addSql('CREATE INDEX locator ON bookings (locator)');
        $this->addSql('CREATE UNIQUE INDEX booking_id ON bookings (booking_id)');
        $this->addSql('CREATE UNIQUE INDEX passport ON guests (passport)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX hotel_id ON bookings');
        $this->addSql('DROP INDEX locator ON bookings');
        $this->addSql('DROP INDEX booking_id ON bookings');
        $this->addSql('DROP INDEX passport ON guests');
    }
}
