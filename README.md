# Job Dating App

Plateforme PHP minimale (Twig) avec flux etudiant et admin. Ce projet fournit une experience front-office pour les etudiants et un back-office pour gerer annonces, entreprises, etudiants et candidatures.

## Prerequis
- PHP >= 8.0
- Composer
- MySQL

## Demarrage rapide
1. Installer les dependances :
   - `composer install`
2. Creer la base et les donnees initiales :
   - Importer `data.sql` dans MySQL.
3. Configurer l'environnement :
   - Creer `.env` avec au minimum :
     - `DB_PASS=your_password`
     - `SESSION_LIFETIME=3600` (optionnel)
4. Lancer l'application (serveur dev) :
   - `php -S localhost:8000 -t public`
5. Ouvrir :
   - `http://localhost:8000/login`

## Environnement et config
- Config principale : `config/config.php`
  - Lit le mot de passe DB via `.env` (`$_ENV['DB_PASS']`).
  - Nom de DB utilise : `job_dating`.
- Exemple `.env` :
  ```
  DB_PASS=secret
  SESSION_LIFETIME=3600
  ```

## Schema base de donnees (resume)
Defini dans `data.sql`.
- `users` : table d'auth avec `role` (`admin` ou `student`).
- `students` : infos etudiant liees a `users`.
- `companies` : entreprises partenaires.
- `announcements` : offres d'emploi.
- `applications` : candidatures des etudiants.
  - `status` enum : `pending`, `accepted`, `rejected`.

## Comptes par defaut
Les mots de passe sont hashes dans `data.sql`.
- Admin :
  - Email : `admin@youcode.com`
- Etudiant :
  - Email : `student@youcode.com`

## Flux candidature (etudiant vers admin)
1. L'etudiant ouvre la page emplois : `/jobs`
2. Il clique "View Details" (modal) puis "Apply".
3. Le formulaire envoie vers `/jobs/apply` (POST).
4. La candidature est enregistree avec `status = pending`.
5. L'admin examine dans `/admin/applications`.
6. L'admin marque la candidature en `accepted` ou `rejected`.
7. Le tableau etudiant affiche :
   - Badge vert "Hired" si `accepted`.
   - Badge rouge "Rejected" si `rejected`.

## Routes principales
Etudiant :
- `/login` (GET/POST)
- `/register` (GET/POST)
- `/jobs` (GET)
- `/jobs/show?id=ID` (GET)
- `/jobs/apply?id=ID` (GET/POST)

Admin :
- `/admin/dashboard`
- `/admin/announcements`
- `/admin/announcements/create`
- `/admin/announcements/edit`
- `/admin/announcements/archived`
- `/admin/companies`
- `/admin/students`
- `/admin/applications`
- `/admin/applications/approve` (POST)
- `/admin/applications/deny` (POST)

## Carte du code
- Routes : `config/routes.php`
- Controllers front : `app/controllers/front`
- Controllers back : `app/controllers/back`
- Models : `app/models`
  - Applications : `app/models/application.php`
- Vues :
  - Etudiant : `app/views/frontend` et `app/views/front`
  - Admin : `app/views/back`
- Assets :
  - JS front etudiant : `public/assets/js/index.js`
  - JS sidebar/theme admin : `public/assets/js/script.js`

## Auth et session
- Classe Auth : `app/core/Auth.php`
  - `requireAuth()` protege les espaces etudiant/admin.
  - `requireAdmin()` protege les routes admin.
  - `requireStudent()` protege les routes etudiant.
- Deconnexion : `POST /logout`

## CSRF
- Token genere par : `App\core\Security::csrfToken()`
- Verifie par : `App\core\Security::verifyCsrfToken($token)`
- Utilise dans tous les formulaires (login, register, admin, apply).

## Front Office
- `app/views/frontend/index.twig` injecte `jobs`, `student` et les statuts dans des globals JS.
- `public/assets/js/index.js` rend toute l'UI etudiant :
  - Navbar, filtres, cartes, modal.
  - Badges "Hired" et "Rejected" selon le statut.

## Back Office
- Items de sidebar admin dans `public/assets/js/script.js` (`NAV_ITEMS`).
- Page candidatures : `app/views/back/applications/index.twig`.

## Depannage
- "CSRF token invalid" :
  - Verifier que le formulaire contient `csrf_token` et que la session est active.
- "Data truncated for column status" :
  - Verifier l'enum : `SHOW CREATE TABLE applications;`
  - Doit etre `pending/accepted/rejected`.
- "Template not found" :
  - Verifier le chemin des vues Twig dans `app/views`.

## Ajouter un element de sidebar
1. Editer `public/assets/js/script.js`
2. Ajouter un item dans `NAV_ITEMS`, par exemple :
   ```
   { href: '/admin/test', icon: 'science', label: 'Test' }
   ```
3. Ajouter la route/le controller/la vue si necessaire.
