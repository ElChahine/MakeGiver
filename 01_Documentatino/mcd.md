graph TD
    %% --- STYLE DES BOITES ---
    classDef box fill:#fff,stroke:#000,stroke-width:1px,align:left;

    %% --- ENTITÉS ---
    U["**UTILISATEUR**<br>_id_user_<br>Nom/Prenom<br>Email/Tel<br>**Consentement_Public (Bool)**<br>Role"]:::box
    P["**PROJET / BESOIN**<br>_id_projet_<br>Titre/Description<br>Statut<br>DemandeurID (FK)"]:::box
    S["**SOLUTION**<br>_id_solution_<br>Titre/Licence<br>CreateurID (FK)<br>**CoAuteurID (FK)**"]:::box
    F["**FICHIER**<br>_id_fichier_<br>Nom/Chemin/Type"]:::box
    L["**ANNUAIRE (Lieu)**<br>_id_lieu_<br>Nom/Ville/Type"]:::box
    E["**AGENDA (Event)**<br>_id_event_<br>Titre/Lien_Externe"]:::box
    COM["**COMMENTAIRE**<br>_id_com_<br>Contenu_Texte<br>Date_Post"]:::box
    SIG["**SIGNALEMENT**<br>_id_sig_<br>Raison/Date"]:::box

    %% --- RELATIONS ---
    
    %% Gestion des Signalements 
    U ---|1,N| R1((Signaler))
    R1 ---|1,1| SIG
    SIG ---|1,1| R2((Concerne))
    R2 --- P
    R2 --- S

    %% Gestion des Fichiers (Liaison aux Besoins et Solutions) 
    P ---|0,N| R3((Lier_Doc))
    R3 --- F
    S ---|0,N| R4((Comporter))
    R4 --- F

    %% Collaboration et Publication [cite: 37, 38]
    U ---|1,N| R5((Soumettre))
    R5 --- P
    U ---|0,N| R6((Publier_Lead))
    R6 --- S
    U ---|0,1| R7((CoAuteur))
    R7 --- S

    %% Interaction Communautaire [cite: 42, 43]
    U ---|0,N| R8((Commenter))
    R8 --- COM
    COM ---|1,1| R9((Sur))
    R9 --- P
    R9 --- S

    %% Note: Les relations vers Lieu et Event sont isolées comme prévu dans tes notes.