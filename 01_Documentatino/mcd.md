graph TD
    %% --- MCD  ---
    classDef box fill:#fff,stroke:#000,stroke-width:1px,align:left;

    %% --- ENTITÉS ---
    U["**UTILISATEUR**<br>_UtilisateurID_<br>Nom/Prenom/Pseudo<br>Email/Telephone<br>Role (Membre/Maker/Soignant/Admin)<br>**Consentement_Public (Bool)**<br>Region/Bio/Competences"]:::box
    P["**PROJET / BESOIN**<br>_ProjetID_<br>Titre_Besoin/Description<br>Statut (Ouvert/En cours/Terminé)<br>DemandeurID (FK)<br>maker_id (FK, maker retenu)"]:::box
    S["**SOLUTION**<br>_SolutionID_<br>Titre/Licence/Difficulte<br>CreateurID (FK)<br>**CoAuteurID (FK)**"]:::box
    CAND["**CANDIDATURE**<br>_id_<br>date_candidature<br>(maker ↔ besoin)"]:::box
    F["**FICHIER**<br>_FichierID_<br>Nom/Chemin/Type/Taille"]:::box
    COM["**COMMENTAIRE**<br>_CommentaireID_<br>Contenu_Texte/Date_Post<br>Est_Valide (modération)"]:::box
    SIG["**SIGNALEMENT**<br>_SignalementID_<br>TypeContenu/ContenuID<br>Raison/Date"]:::box
    L["**LIEU (Annuaire)**<br>_LieuID_<br>Nom/Ville/Type<br>(table dispo ; FabLabs via API)"]:::box
    E["**EVENEMENT (Agenda)**<br>_EvenementID_<br>Titre/Dates<br>Lien_Externe_Organisateur"]:::box
    C["**CATEGORIE**<br>_CategorieID_<br>Nom/Type<br>(présente, non reliée)"]:::box

    %% --- RELATIONS ---

    %% Besoins : dépôt et prise en charge
    U ---|1,N| R1((Soumettre))
    R1 ---|1,1| P
    U ---|0,N| R2((Postuler))
    R2 ---|1,1| CAND
    CAND ---|1,1| R3((Sur))
    R3 --- P
    U ---|0,1| R4((Maker retenu))
    R4 --- P

    %% Solutions : publication et co-auteur
    U ---|0,N| R5((Publier))
    R5 --- S
    U ---|0,1| R6((Co-auteur))
    R6 --- S

    %% Fichiers joints (besoin ou solution)
    P ---|0,N| R7((Joindre))
    R7 --- F
    S ---|0,N| R8((Comporter))
    R8 --- F

    %% Commentaires (sur un besoin ou une solution), modérés
    U ---|0,N| R9((Rédiger))
    R9 --- COM
    COM ---|1,1| R10((Sur))
    R10 --- P
    R10 --- S

    %% Signalements : un utilisateur signale un contenu
    U ---|0,N| R11((Signaler))
    R11 --- SIG
    SIG ---|1,1| R12((Concerne))
    R12 --- S
    R12 --- P
    R12 --- COM
    R12 --- E
    R12 --- U

    %% Note : LIEU, EVENEMENT et CATEGORIE ne sont pas reliés dans l'application actuelle
    %% (annuaire FabLabs alimenté par l'API externe ; événements externes ; catégories non branchées).
