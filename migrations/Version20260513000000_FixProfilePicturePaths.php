<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260513000000_FixProfilePicturePaths extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix profile picture paths by extracting just the filename from full paths';
    }

    public function up(Schema $schema): void
    {
        // Extract just the filename from any profile pictures that have full paths
        $this->addSql(
            "UPDATE users 
             SET profile_picture = SUBSTR(profile_picture, INSTR(profile_picture, '/') + 1)
             WHERE profile_picture IS NOT NULL 
             AND profile_picture LIKE '%/%'"
        );

        // For Windows paths with backslashes
        $this->addSql(
            "UPDATE users 
             SET profile_picture = SUBSTR(profile_picture, INSTR(profile_picture, '\\\\') + 1)
             WHERE profile_picture IS NOT NULL 
             AND profile_picture LIKE '%\\\\%'"
        );
    }

    public function down(Schema $schema): void
    {
        // No rollback needed for this fix
    }
}
