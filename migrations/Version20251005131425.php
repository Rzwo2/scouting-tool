<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251005131425 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, team_one_id INT NOT NULL, team_two_id INT NOT NULL, date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_232B318C8D8189CA (team_one_id), INDEX IDX_232B318CE6DD6E05 (team_two_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_set (id INT AUTO_INCREMENT NOT NULL, game_id INT NOT NULL, set_number SMALLINT NOT NULL, points_team_one SMALLINT NOT NULL, points_team_two SMALLINT NOT NULL, duration_minutes SMALLINT DEFAULT NULL, INDEX IDX_FD4E3619E48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player (id INT AUTO_INCREMENT NOT NULL, team_id INT NOT NULL, name VARCHAR(255) NOT NULL, number INT NOT NULL, INDEX IDX_98197A65296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player_game_statistic (id INT AUTO_INCREMENT NOT NULL, player_id INT NOT NULL, game_id INT NOT NULL, sets_played SMALLINT NOT NULL, is_first_ball_side_out TINYINT(1) NOT NULL, attack_kill SMALLINT DEFAULT NULL, attack_error SMALLINT DEFAULT NULL, attack_total SMALLINT DEFAULT NULL, serve_ace SMALLINT DEFAULT NULL, serve_error SMALLINT DEFAULT NULL, serve_total SMALLINT DEFAULT NULL, serve_rating DOUBLE PRECISION DEFAULT NULL, receive3 SMALLINT DEFAULT NULL, receive2 SMALLINT DEFAULT NULL, receive1 SMALLINT DEFAULT NULL, receive0 SMALLINT DEFAULT NULL, set_assist SMALLINT DEFAULT NULL, set_total SMALLINT DEFAULT NULL, dig_success SMALLINT DEFAULT NULL, dig_error SMALLINT DEFAULT NULL, block_single SMALLINT DEFAULT NULL, block_assist SMALLINT DEFAULT NULL, block_error SMALLINT DEFAULT NULL, INDEX IDX_7AE4107799E6F5DF (player_id), INDEX IDX_7AE41077E48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, city VARCHAR(255) DEFAULT NULL, address_street VARCHAR(255) DEFAULT NULL, address_number VARCHAR(50) DEFAULT NULL, address_suffix VARCHAR(255) DEFAULT NULL, address_zip VARCHAR(10) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C8D8189CA FOREIGN KEY (team_one_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CE6DD6E05 FOREIGN KEY (team_two_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE game_set ADD CONSTRAINT FK_FD4E3619E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE player ADD CONSTRAINT FK_98197A65296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE player_game_statistic ADD CONSTRAINT FK_7AE4107799E6F5DF FOREIGN KEY (player_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE player_game_statistic ADD CONSTRAINT FK_7AE41077E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
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
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE game_set');
        $this->addSql('DROP TABLE player');
        $this->addSql('DROP TABLE player_game_statistic');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
