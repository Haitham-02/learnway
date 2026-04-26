<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260425111645 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'No-op: align ORM cardinality (SubjectSection many-to-one) with existing schema.';
    }

    public function up(Schema $schema): void
    {
        // No schema change required: database already supports the updated cardinality.
    }

    public function down(Schema $schema): void
    {
        // No-op.
    }
}
