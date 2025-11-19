<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251106222134 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE address (id INT AUTO_INCREMENT NOT NULL, zip VARCHAR(11) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, street VARCHAR(255) DEFAULT NULL, suffix VARCHAR(255) DEFAULT NULL, number VARCHAR(10) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_D4E6F81F0EED3D896901F54B5B087DE421D95462D5B02345373C966 (street, number, suffix, zip, city, country), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, team_one_id INT NOT NULL, team_two_id INT NOT NULL, game_id VARCHAR(20) NOT NULL, date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_232B318CE48FD905 (game_id), INDEX IDX_232B318C8D8189CA (team_one_id), INDEX IDX_232B318CE6DD6E05 (team_two_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_set (id INT AUTO_INCREMENT NOT NULL, game_id INT NOT NULL, set_number SMALLINT NOT NULL, points_team_one SMALLINT NOT NULL, points_team_two SMALLINT NOT NULL, duration_minutes SMALLINT DEFAULT NULL, INDEX IDX_FD4E3619E48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player (id INT AUTO_INCREMENT NOT NULL, team_id INT NOT NULL, player_id VARCHAR(20) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, number INT NOT NULL, height INT NOT NULL, birth_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', position VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_98197A6599E6F5DF (player_id), INDEX IDX_98197A65296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player_game_statistic (id INT AUTO_INCREMENT NOT NULL, player_id INT NOT NULL, game_id INT NOT NULL, sets_played SMALLINT NOT NULL, is_first_ball_side_out TINYINT(1) NOT NULL, attack_kills SMALLINT DEFAULT NULL, attack_errors SMALLINT DEFAULT NULL, attack_attempts SMALLINT DEFAULT NULL, serve_aces SMALLINT DEFAULT NULL, serve_errors SMALLINT DEFAULT NULL, serve1s SMALLINT DEFAULT NULL, serve_attempts SMALLINT DEFAULT NULL, serve_rating DOUBLE PRECISION DEFAULT NULL, receive3s SMALLINT DEFAULT NULL, receive2s SMALLINT DEFAULT NULL, receive1s SMALLINT DEFAULT NULL, receive0s SMALLINT DEFAULT NULL, receive_attempts SMALLINT DEFAULT NULL, set_assists SMALLINT DEFAULT NULL, set_attempts SMALLINT DEFAULT NULL, dig_successs SMALLINT DEFAULT NULL, dig_errors SMALLINT DEFAULT NULL, block_block_solos SMALLINT DEFAULT NULL, block_block_assists SMALLINT DEFAULT NULL, block_block_errors SMALLINT DEFAULT NULL, INDEX IDX_7AE4107799E6F5DF (player_id), INDEX IDX_7AE41077E48FD905 (game_id), UNIQUE INDEX UNIQ_7AE4107799E6F5DFE48FD90526C5B9E6 (player_id, game_id, is_first_ball_side_out), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE registration_invitation (id INT AUTO_INCREMENT NOT NULL, registered_user_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, token VARCHAR(255) NOT NULL, expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_CC26A88B5F37A13B (token), UNIQUE INDEX UNIQ_CC26A88BA6A12EC1 (registered_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team (id INT AUTO_INCREMENT NOT NULL, address_id INT DEFAULT NULL, team_id VARCHAR(20) NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_C4E0A61F296CD8AE (team_id), UNIQUE INDEX UNIQ_C4E0A61F5E237E06 (name), INDEX IDX_C4E0A61FF5B7AF75 (address_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(50) NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C8D8189CA FOREIGN KEY (team_one_id) REFERENCES team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CE6DD6E05 FOREIGN KEY (team_two_id) REFERENCES team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_set ADD CONSTRAINT FK_FD4E3619E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE player ADD CONSTRAINT FK_98197A65296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE player_game_statistic ADD CONSTRAINT FK_7AE4107799E6F5DF FOREIGN KEY (player_id) REFERENCES player (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE player_game_statistic ADD CONSTRAINT FK_7AE41077E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registration_invitation ADD CONSTRAINT FK_CC26A88BA6A12EC1 FOREIGN KEY (registered_user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61FF5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C8D8189CA');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CE6DD6E05');
        $this->addSql('ALTER TABLE game_set DROP FOREIGN KEY FK_FD4E3619E48FD905');
        $this->addSql('ALTER TABLE player DROP FOREIGN KEY FK_98197A65296CD8AE');
        $this->addSql('ALTER TABLE player_game_statistic DROP FOREIGN KEY FK_7AE4107799E6F5DF');
        $this->addSql('ALTER TABLE player_game_statistic DROP FOREIGN KEY FK_7AE41077E48FD905');
        $this->addSql('ALTER TABLE registration_invitation DROP FOREIGN KEY FK_CC26A88BA6A12EC1');
        $this->addSql('ALTER TABLE team DROP FOREIGN KEY FK_C4E0A61FF5B7AF75');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE game_set');
        $this->addSql('DROP TABLE player');
        $this->addSql('DROP TABLE player_game_statistic');
        $this->addSql('DROP TABLE registration_invitation');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
