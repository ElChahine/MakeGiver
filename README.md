# Je Demain (MakeGiver) — Lancer l'application en local

Application web **Symfony 8** (PHP ≥ 8.4) avec base **MySQL**, lancée en local avec **Laragon** (serveur MySQL + phpMyAdmin) et **cmder** (terminal).

---

## 1. Prérequis (Laragon)

Installer **Laragon (Full)**, qui fournit MySQL, phpMyAdmin et Composer. Vérifier ensuite :

- **PHP ≥ 8.4** — important, le projet l'exige. Dans Laragon : clic droit sur l'icône → **PHP → Version** et choisir une version **8.4** ou plus. (S'il n'y en a pas, l'ajouter via **Tools → Quick add → PHP**.)
- **Composer** — fourni avec Laragon (sinon : https://getcomposer.org).
- *(Optionnel mais conseillé)* **Symfony CLI** : https://symfony.com/download — pratique pour lancer le serveur.

> Par défaut, le MySQL de Laragon utilise l'utilisateur **`root`**, **sans mot de passe**, sur le port **3306** — ce qui correspond déjà à la configuration du projet.

---

## 2. Récupérer le projet

Placer le dossier du projet où vous voulez (par exemple dans `C:\laragon\www\`). Le code de l'application se trouve dans :

```
02_Application/makegiver
```

Toutes les commandes ci-dessous se lancent **depuis ce dossier**, dans **cmder**.

```bash
cd C:\chemin\vers\projet PRO\02_Application\makegiver
php -v        # doit afficher PHP 8.4 ou plus
```

---

## 3. Installer les dépendances

```bash
composer install
```

> Si Composer se plaint de la version de PHP, c'est que Laragon n'est pas sur PHP 8.4+ : revoir l'étape 1.

---

## 4. Configurer la base de données

La configuration par défaut (fichier `.env`) pointe déjà vers le MySQL de Laragon :

```
DATABASE_URL="mysql://root:@127.0.0.1:3306/makegiver?serverVersion=8.0.32&charset=utf8mb4"
```

Si votre MySQL a un mot de passe ou un autre port, **ne pas modifier `.env`** : créer un fichier **`.env.local`** (non versionné) avec votre propre ligne `DATABASE_URL`.

---

## 5. Créer la base et importer les données

Le contenu de référence (structure **+ jeu de données de test**) est dans :

```
01_Documentatino/makegiver.sql
```

### Méthode recommandée — phpMyAdmin (via Laragon)

1. Démarrer Laragon (**Start All**).
2. Ouvrir **phpMyAdmin** (bouton **Database** de Laragon, ou menu **MySQL → phpMyAdmin**).
3. Créer une base nommée **`makegiver`** (interclassement `utf8mb4_general_ci`).
4. Sélectionner cette base → onglet **Importer** → choisir `01_Documentatino/makegiver.sql` → **Exécuter**.

### Méthode alternative — en ligne de commande (cmder)

```bash
php bin/console doctrine:database:create
mysql -u root makegiver < "..\..\01_Documentatino\makegiver.sql"
```

> Le projet contient aussi des migrations Doctrine, mais le **dump SQL est la référence** (il inclut les données de démonstration). Privilégier l'import du dump.

---

## 6. Vider le cache

```bash
php bin/console cache:clear
```

> Les fichiers front (CSS/JS) sont gérés par **AssetMapper** : aucun `npm install` n'est nécessaire. En cas de souci d'assets : `php bin/console importmap:install`.

---

## 7. Lancer l'application

### Avec Symfony CLI (recommandé)

```bash
symfony server:start
```

Puis ouvrir l'adresse affichée, en général **https://127.0.0.1:8000**.

### Sans Symfony CLI (serveur PHP intégré)

```bash
php -S 127.0.0.1:8000 -t public
```

Puis ouvrir **http://127.0.0.1:8000**.

> Si vous préférez passer par Apache/Nginx de Laragon, placez le projet dans `C:\laragon\www\` et pointez le *document root* du site sur le dossier `public/`.

---

## 8. Se connecter

La base importée contient des comptes de démonstration (dont un compte administrateur `admin@gmail.fr` avec mdp: `admin1234`). Les **mots de passe sont chiffrés** : si vous ne les connaissez pas, le plus simple est de :

1. Créer un compte via la page **Inscription** (`/inscription`).
2. Pour obtenir les droits administrateur : dans **phpMyAdmin**, table `utilisateurs`, mettre la colonne **`Role`** de votre compte à **`Admin`**.

Vous accédez alors à l'espace d'administration (statistiques, modération des signalements) via le menu.

---

## 9. Dépannage rapide

- **Erreur « Unknown column / table »** → la base n'a pas été importée, ou pas sous le nom `makegiver`. Refaire l'étape 5.
- **Composer refuse d'installer** → PHP n'est pas en 8.4+ (étape 1).
- **Page blanche / erreur après modification du `.env`** → `php bin/console cache:clear`.
- **Le port 8000 ou 3306 est déjà utilisé** → changer le port (`symfony server:start --port=8001`, ou ajuster `DATABASE_URL`).
- **Échec d'envoi de fichier sur une solution** → vérifier que le dossier `public/uploads/solutions/` existe et est accessible en écriture.
- **Carte des FabLabs vide** → l'annuaire interroge une API externe ; vérifier la connexion Internet.

---

## Structure utile

```
02_Application/makegiver/
├─ public/            # racine web (index.php) + uploads
├─ src/               # code PHP (contrôleurs, entités, formulaires)
├─ templates/         # vues Twig
├─ migrations/        # migrations Doctrine
├─ .env               # configuration par défaut (ne pas committer de secrets)
01_Documentatino/
└─ makegiver.sql      # base de référence à importer
```
