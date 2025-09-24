# fawtrasmart


## Installation and Setup

### Cleaning Laravel Project

To clean your Laravel project and ensure optimal functionality:
1. **Clear all caches:**
```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear

```
2. **Reinstall dependencies** 
```bash
    rm -rf vendor
    composer install

```

3. **Clean frontend dependencies (if using npm)** 
```bash
    rm -rf node_modules
    npm install

```
## Database Setup

1. **Configure database settings in .env file** 
```bash
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database
    DB_USERNAME=your_username
    DB_PASSWORD=your_password


```
2. **Run migrations to set up the database** 
```bash
  php artisan migrate

```
## Running Laravel Project
```bash
   php artisan serve

```
