<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260426123908 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Superseded by Version20260426162553; intentionally no-op to avoid duplicate schema operations.';
    }

    public function up(Schema $schema): void
    {
        // Intentionally left blank.
    }

    public function down(Schema $schema): void
    {
        // Intentionally left blank.
    }
}
