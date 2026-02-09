# Wispware 🚀

> Modern development stack for Laravel 12 + Filament running on Docker, optimized for Raspberry Pi and local development.

---

## 🧠 Overview

Wispware is a **production-grade development environment** built on Docker, designed for modern Laravel applications using:

- Laravel 12  
- Filament  
- Vite  
- Node.js  
- MySQL  
- Nginx  

It provides:

- 🔥 Fast local development  
- 🐳 Fully containerized stack  
- 🔐 Correct file permissions using host UID/GID  
- ⚡ Optimized for Raspberry Pi 5  
- 🛠 Clean and scalable architecture  

---

## 🏗 Architecture

Docker
├── PHP 8.2 (Laravel backend)
├── Node 22 (Vite frontend)
├── Nginx (HTTP server)
└── MySQL 8 (database)


Each service runs in its own container and communicates through an isolated Docker network.

---


## ✨ Features

- Dockerized development environment  
- PHP 8.2 + Laravel 12  
- Filament admin panel ready  
- Node.js 22 + Vite  
- Hot reload support  
- Correct UID/GID mapping to prevent permission issues  
- Optimized performance for ARM64 (Raspberry Pi)  
- Clean, modular infrastructure  

---


## 🛠 Stack

| Component | Version |
|-------------|-----------|
| PHP | 8.2 |
| Laravel | 12 |
| Node.js | 22 |
| Nginx | latest |
| MySQL | 8.0 |
| Docker Compose | v2 |

---


## 📁 Project Structure

wispware
├── docker-compose.yml
├── .env
├── services
│ ├── php
│ ├── node
│ ├── nginx
│ └── mysql
└── src


---


## 🚀 Quick Start

### 1️⃣ Clone repository

```bash
git clone https://github.com/your-user/wispware.git
cd wispware
2️⃣ Configure environment variables
Create .env file:

UID=1000
GID=1000
These values must match your host system user to avoid permission issues.

3️⃣ Build and start containers
docker compose build
docker compose up -d
4️⃣ Install Laravel
docker compose exec php composer create-project laravel/laravel .
5️⃣ Install frontend dependencies
docker compose exec node npm install
docker compose exec node npm run dev
6️⃣ Open in browser
http://localhost:8080
🧪 Common Commands
Artisan
docker compose exec php php artisan migrate
docker compose exec php php artisan make:model Test -m
Composer
docker compose exec php composer install
docker compose exec php composer require filament/filament
Node / Vite
docker compose exec node npm install
docker compose exec node npm run dev
🔐 Permissions Handling
This stack uses host UID/GID mapping to ensure:

No sudo needed on generated files

No root-owned files

Perfect compatibility with host filesystem

Configured using:

user: "${UID}:${GID}"
⚙️ Why This Architecture?
Separate PHP and Node containers for clean responsibility separation

Lightweight containers

High performance

Clean scaling

Enterprise-ready structure

📌 Roadmap
 Filament admin panel integration

 Authentication with Laravel Breeze

 Redis + Queues

 Horizon

 RouterOS API integration

 CI/CD pipeline

🧑‍💻 Author
Built by rp-server
Optimized for professional development and production-grade workflows.
