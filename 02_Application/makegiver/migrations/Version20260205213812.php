<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260205213812 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE commentaires DROP FOREIGN KEY `commentaires_ibfk_1`');
        $this->addSql('ALTER TABLE commentaires DROP FOREIGN KEY `commentaires_ibfk_2`');
        $this->addSql('ALTER TABLE commentaires DROP FOREIGN KEY `commentaires_ibfk_3`');
        $this->addSql('ALTER TABLE fichiers DROP FOREIGN KEY `fichiers_ibfk_1`');
        $this->addSql('ALTER TABLE fichiers DROP FOREIGN KEY `fichiers_ibfk_2`');
        $this->addSql('ALTER TABLE membresprojet DROP FOREIGN KEY `membresprojet_ibfk_1`');
        $this->addSql('ALTER TABLE membresprojet DROP FOREIGN KEY `membresprojet_ibfk_2`');
        $this->addSql('ALTER TABLE projets DROP FOREIGN KEY `projets_ibfk_1`');
        $this->addSql('ALTER TABLE signalements DROP FOREIGN KEY `signalements_ibfk_1`');
        $this->addSql('ALTER TABLE solutions DROP FOREIGN KEY `solutions_ibfk_1`');
        $this->addSql('ALTER TABLE solutions DROP FOREIGN KEY `solutions_ibfk_2`');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE commentaires');
        $this->addSql('DROP TABLE evenements');
        $this->addSql('DROP TABLE fichiers');
        $this->addSql('DROP TABLE lieux');
        $this->addSql('DROP TABLE membresprojet');
        $this->addSql('DROP TABLE projets');
        $this->addSql('DROP TABLE signalements');
        $this->addSql('DROP TABLE solutions');
        $this->addSql('DROP TABLE utilisateurs');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categories (CategorieID INT AUTO_INCREMENT NOT NULL, Nom_Categorie VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Type_Categorie VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Description TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, PRIMARY KEY (CategorieID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE commentaires (CommentaireID INT AUTO_INCREMENT NOT NULL, Contenu_Texte TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Date_Post DATETIME DEFAULT CURRENT_TIMESTAMP, Est_Valide TINYINT DEFAULT 1, AuteurID INT DEFAULT NULL, SolutionID INT DEFAULT NULL, ProjetID INT DEFAULT NULL, INDEX AuteurID (AuteurID), INDEX SolutionID (SolutionID), INDEX ProjetID (ProjetID), PRIMARY KEY (CommentaireID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE evenements (EvenementID INT AUTO_INCREMENT NOT NULL, Titre_Event VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Description TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Date_Debut DATETIME DEFAULT NULL, Date_Fin DATETIME DEFAULT NULL, Type VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Lien_Externe_Organisateur VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, PRIMARY KEY (EvenementID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE fichiers (FichierID INT AUTO_INCREMENT NOT NULL, Nom_Fichier VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Chemin_URL VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Type VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Taille_Mo NUMERIC(10, 2) DEFAULT NULL, SolutionID INT DEFAULT NULL, ProjetID INT DEFAULT NULL, INDEX SolutionID (SolutionID), INDEX ProjetID (ProjetID), PRIMARY KEY (FichierID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE lieux (LieuID INT AUTO_INCREMENT NOT NULL, Nom_Lieu VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Adresse_Complete VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Code_Postal VARCHAR(10) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Ville VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Coordonnees_GPS VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Type VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Liste_Equipements TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, PRIMARY KEY (LieuID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE membresprojet (ProjetID INT NOT NULL, UtilisateurID INT NOT NULL, RoleSurProjet VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, INDEX UtilisateurID (UtilisateurID), INDEX IDX_C2015AF86ED989B6 (ProjetID), PRIMARY KEY (ProjetID, UtilisateurID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE projets (ProjetID INT AUTO_INCREMENT NOT NULL, Titre_Besoin VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Description_Detaillee TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Statut ENUM(\'Ouvert\', \'En cours\', \'Terminé\') CHARACTER SET utf8mb4 DEFAULT \'Ouvert\' COLLATE `utf8mb4_0900_ai_ci`, Date_Creation DATETIME DEFAULT CURRENT_TIMESTAMP, Date_MiseAJour DATETIME DEFAULT NULL, DemandeurID INT DEFAULT NULL, INDEX DemandeurID (DemandeurID), PRIMARY KEY (ProjetID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE signalements (SignalementID INT AUTO_INCREMENT NOT NULL, UtilisateurID INT DEFAULT NULL, TypeContenu VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, ContenuID INT DEFAULT NULL, Raison TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Date_Signalement DATETIME DEFAULT CURRENT_TIMESTAMP, INDEX UtilisateurID (UtilisateurID), PRIMARY KEY (SignalementID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE solutions (SolutionID INT AUTO_INCREMENT NOT NULL, Titre_Solution VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Description_Technique TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Licence VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Materiel_Necessaire TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Difficulte_Fabrication VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Date_Publication DATETIME DEFAULT CURRENT_TIMESTAMP, Est_CoupDeCoeur TINYINT DEFAULT 0, CreateurID INT DEFAULT NULL, CoAuteurID INT DEFAULT NULL, INDEX CreateurID (CreateurID), INDEX CoAuteurID (CoAuteurID), PRIMARY KEY (SolutionID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE utilisateurs (UtilisateurID INT AUTO_INCREMENT NOT NULL, Nom VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Prenom VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Email VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Telephone VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, MotDePasse_Hash VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Role ENUM(\'Patient\', \'Maker\', \'Soignant\', \'Admin\') CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Bio_Description TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Competences_Techniques TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, Consentement_Public TINYINT DEFAULT 0, Date_Inscription DATETIME DEFAULT CURRENT_TIMESTAMP, UNIQUE INDEX Email (Email), PRIMARY KEY (UtilisateurID)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE commentaires ADD CONSTRAINT `commentaires_ibfk_1` FOREIGN KEY (AuteurID) REFERENCES utilisateurs (UtilisateurID) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE commentaires ADD CONSTRAINT `commentaires_ibfk_2` FOREIGN KEY (SolutionID) REFERENCES solutions (SolutionID) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE commentaires ADD CONSTRAINT `commentaires_ibfk_3` FOREIGN KEY (ProjetID) REFERENCES projets (ProjetID) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE fichiers ADD CONSTRAINT `fichiers_ibfk_1` FOREIGN KEY (SolutionID) REFERENCES solutions (SolutionID) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE fichiers ADD CONSTRAINT `fichiers_ibfk_2` FOREIGN KEY (ProjetID) REFERENCES projets (ProjetID) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE membresprojet ADD CONSTRAINT `membresprojet_ibfk_1` FOREIGN KEY (ProjetID) REFERENCES projets (ProjetID) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE membresprojet ADD CONSTRAINT `membresprojet_ibfk_2` FOREIGN KEY (UtilisateurID) REFERENCES utilisateurs (UtilisateurID) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE projets ADD CONSTRAINT `projets_ibfk_1` FOREIGN KEY (DemandeurID) REFERENCES utilisateurs (UtilisateurID) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE signalements ADD CONSTRAINT `signalements_ibfk_1` FOREIGN KEY (UtilisateurID) REFERENCES utilisateurs (UtilisateurID) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE solutions ADD CONSTRAINT `solutions_ibfk_1` FOREIGN KEY (CreateurID) REFERENCES utilisateurs (UtilisateurID) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE solutions ADD CONSTRAINT `solutions_ibfk_2` FOREIGN KEY (CoAuteurID) REFERENCES utilisateurs (UtilisateurID) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
