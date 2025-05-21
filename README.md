# Todo Application (Laravel)

A Todo application built using the Laravel framework.

---

## Requirements

Make sure the following tools are installed:

* **Node**: `20.19.1`
* **NPM**: `10.8.2`
* **Docker**: `26.1.3`
* **Docker Compose**: `1.25.0`

---

## Installation

### 1. Clone the Repository

```bash
git clone git@github.com:artisozolin/todo-app.git
cd todo-app
```

### 2. Build and Start Docker Containers

```bash
docker-compose build
docker-compose up -d
```

### 3. Install Composer Dependencies

```bash
docker exec -it laravel-app composer install
```

### 4. Configure Environment

Copy the example environment file and adjust values if needed:

```bash
cp .env.example .env
```

### 5. Generate Laravel Application Key

```bash
docker exec -it laravel-app php artisan key:generate
```

### 6. Run Database Migrations

```bash
docker exec -it laravel-app php artisan migrate:fresh
```

### 7. Fix File Permissions

```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R $USER:www-data storage bootstrap/cache
```

---

## Frontend Setup

### 1. Install NPM Dependencies

```bash
npm install
```

### 2. Compile Assets

This command should be run every time to compile styling change.

```bash
npm run dev
```
