# 🚀 Kalangka Server - Laravel Local Setup with PostgreSQL & pgAdmin

## 📋 Prerequisites

### Install these first: 
- ✅ **XAMPP** (https://www.apachefriends.org/download.html)
- ✅ **PHP 8.1+** (https://windows.php.net/download/)
- ✅ **Composer** (https://getcomposer.org/)
- ✅ **Git** (https://git-scm.com/downloads)
- ✅ **PostgreSQL** (https://www.postgresql.org/download/windows/)
- ✅ **pgAdmin** (kasama na sa PostgreSQL installer)

## ⚡ Step-by-Step Local Setup

### Step 1: Clone the Repository
```bash
# Open CMD or PowerShell
cd C:\xampp\htdocs  # or your preferred directory

# Clone the project
git clone https://github.com/yourusername/kalangka-server.git

# Go to project folder
cd kalangka-server
```

### Step 2: Environment Setup
```bash
# Copy .env file
copy .env.example .env
```

Edit `.env` file with PostgreSQL configuration:
```env
# Application
APP_NAME=KalangkaServer
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration for PostgreSQL
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=kalangka_db
DB_USERNAME=postgres
DB_PASSWORD=postgres  # Your PostgreSQL password

# Optional: If using different user
# DB_USERNAME=your_username
# DB_PASSWORD=your_password
```

### Step 3: Install PHP Dependencies
```bash
composer install
```

### Step 4: Create Database using pgAdmin

**Via pgAdmin (GUI):**
1. Open **pgAdmin** (search in Start Menu)
2. Enter your master password (set during installation)
3. Expand **Servers** > **PostgreSQL** (right-click, connect)
4. Enter your PostgreSQL password
5. Right-click **Databases** > **Create** > **Database**
6. Database name: `kalangka_db`
7. Owner: `postgres` (or your username)
8. Click **Save**

**Via Command Line (Alternative):**
```bash
# Open SQL Shell (psql) or CMD
psql -U postgres
# Enter password when prompted

# Create database
CREATE DATABASE kalangka_db;
\q
```

### Step 5: Generate Application Key
```bash
php artisan key:generate
```

### Step 6: Run Migrations
```bash
# Create database tables
php artisan migrate

# If you want sample data
php artisan db:seed
```



### Step 7: Run the Application
```bash
# Start Laravel development server
php artisan serve

# You'll see: Laravel development server started: http://127.0.0.1:8000
```

## 🎯 Access Your Application

| Service | URL | Credentials |
|---------|-----|-------------|
| **Laravel App** | http://127.0.0.1:8000 | - |
| **pgAdmin** | http://localhost:5050 | Set during installation |
| **PostgreSQL** | localhost:5432 | User: postgres, Pass: yourpassword |




## ✅ Post-Setup Checklist

- [ ] PostgreSQL service is running
- [ ] Database `kalangka_db` exists
- [ ] Can connect using pgAdmin
- [ ] `.env` has correct database credentials
- [ ] `php artisan migrate` works
- [ ] `php artisan serve` starts the server
- [ ] Can access http://localhost:8000
- [ ] Try accessing an api http://localhost:8000/api/trees/ (Should fetch all the trees)


**🎉 Your Kalangka Server is now running with PostgreSQL!** 
**Open pgAdmin to manage your database easily!** 🐘

##### DISREGARD the items below,  backup ko lang to 
Gawa ng Procfile ito laman
web: heroku-php-apache2 public/