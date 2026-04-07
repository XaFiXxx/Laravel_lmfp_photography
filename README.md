# 📸 LMFP Photography

Application web fullstack développée avec **React** et **Laravel** pour la gestion de contenus photographiques (articles, galeries, profils utilisateurs).

---

## 🚀 Fonctionnalités

### 🔐 Authentification

* Inscription utilisateur
* Connexion sécurisée
* Vérification d’email via mail (Resend)
* Blocage de connexion si email non vérifié
* Gestion des rôles (visiteur, mannequin, photographe, organisateur)
* Dashboard administrateur

### 📝 Contenu

* Création / modification / suppression de posts
* Upload d’images (image principale + galerie)
* Gestion des catégories
* Affichage des articles et galeries

### 👤 Utilisateur

* Profil utilisateur
* Upload d’avatar
* Gestion des informations personnelles

---

## 🧱 Stack technique

### Frontend

* React
* React Router
* Tailwind CSS
* Axios

### Backend

* Laravel
* Laravel Sanctum (auth API)
* Eloquent ORM

### Base de données

* MySQL

### Services externes

* Resend (envoi d’emails)

---

## ⚙️ Installation locale

### 🔙 Backend (Laravel)

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

---

### 🔜 Frontend (React)

```bash
npm install
npm start
```

---

## 🔧 Configuration

### Backend `.env`

```env
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:3000

DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...

MAIL_MAILER=smtp
MAIL_HOST=smtp.resend.com
MAIL_PORT=587
MAIL_USERNAME=resend
MAIL_PASSWORD=YOUR_API_KEY
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="LMFP Photography"
```

---

### Frontend `.env`

```env
REACT_APP_API_URL=http://localhost:8000/api
REACT_APP_IMG_URL=http://localhost:8000
```

---

## 📧 Vérification d’email

* Lors de l’inscription, un email de confirmation est envoyé
* L’utilisateur doit cliquer sur le lien pour activer son compte
* Tant que l’email n’est pas vérifié :

  * la connexion est bloquée
  * certaines routes sont protégées

---

## 📁 Gestion des images

Les images sont stockées directement dans :

```
public/storage/img/
```

Sans utilisation de lien symbolique (`storage:link`)

---

## 🚀 Déploiement

Application déployée sur :

* OVH (hébergement backend Laravel)
* Frontend React buildé et déployé
* Base de données distante
* SMTP via Resend

---

## 🔐 Sécurité

* Authentification via token (Sanctum)
* Vérification email obligatoire
* Protection des routes sensibles
* Gestion des rôles (admin / user)

---

## 👨‍💻 Auteur

Benjamin Schroeders

Projet réalisé dans le cadre d’un apprentissage en développement web fullstack.
