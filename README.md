# E-COMMERCE API - LARAVEL

RESTful API untuk sistem e-commerce dengan fitur autentikasi, manajemen produk, checkout, dan integrasi payment gateway DOKU.

=============================================
DEMO
=============================================

Live API: https://web-production-e35b7.up.railway.app/

=============================================
FEATURES
=============================================

- User Authentication (Register, Login, Logout) dengan Laravel Sanctum
- Product Management (CRUD operations)
- Shopping Cart & Checkout System
- Order Management dengan invoice generation
- Payment Gateway Integration (DOKU)
- Webhook Notification untuk status pembayaran
- Custom Access Key Middleware untuk security
- Database Seeding untuk sample data
- API Documentation dengan Postman Collection

=============================================
TECH STACK
=============================================

Backend:
- Laravel 12.x
- PHP 8.2+
- PostgreSQL 16.x
- Laravel Sanctum (Authentication)

Deployment:
- Railway (Platform-as-a-Service)
- PostgreSQL Railway Database

Payment Gateway:
- DOKU Payment Gateway

=============================================
REQUIREMENTS
=============================================

- PHP >= 8.2
- Composer
- PostgreSQL >= 14
- Git

=============================================
INSTALLATION (LOCAL DEVELOPMENT)
=============================================
1. Clone Repository

git clone https://github.com/rizko-d/ecommerce-api.git
cd ecommerce-api


2. Install Dependencies

composer install


3. Environment Setup

cp .env.example .env
php artisan key:generate


4. Configure Database

Edit .env file:

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=ecommerce_local
DB_USERNAME=postgres
DB_PASSWORD=your_password


5. Run Migrations & Seeders

php artisan migrate
php artisan db:seed
# Or run both at once
php artisan migrate:fresh --seed


6. Start Development Server

php artisan serve

API akan berjalan di: http://localhost:8000

=============================================
DEPLOYMENT (RAILWAY)
=============================================

Prerequisites:
1. GitHub repository
2. Railway account (https://railway.app)
3. DOKU Production credentials

Deployment Steps:

1. Push to GitHub:
   git add .
   git commit -m "Deploy to Railway"
   git push origin main

2. Create Railway Project:
   - Login ke Railway
   - New Project -> Deploy from GitHub
   - Select repository -> Deploy

3. Add PostgreSQL Database:
   - + New -> Database -> Add PostgreSQL

4. Configure Environment Variables:

   APP_NAME=E-commerce API
   APP_ENV=production
   APP_KEY=base64:your-generated-key
   APP_DEBUG=false
   APP_URL=https://${{RAILWAY_PUBLIC_DOMAIN}}
   
   DB_CONNECTION=pgsql
   DB_HOST=${{Postgres.PGHOST}}
   DB_PORT=${{Postgres.PGPORT}}
   DB_DATABASE=${{Postgres.PGDATABASE}}
   DB_USERNAME=${{Postgres.PGUSER}}
   DB_PASSWORD=${{Postgres.PGPASSWORD}}
   
   LOG_CHANNEL=errorlog
   CACHE_STORE=file
   SESSION_DRIVER=file
   
   ACCESS_KEY=your-production-access-key
   DOKU_CLIENT_ID=your-doku-client-id
   DOKU_SECRET_KEY=your-doku-secret-key

5. Generate Public Domain:
   Settings -> Networking -> Generate Domain

6. Seed Production Data:
   railway run php artisan db:seed --class=ProductSeeder

=============================================
API DOCUMENTATION
=============================================

Base URL:
Production: https://web-production-e35b7.up.railway.app/
Local: http://localhost:8000

Authentication:
Semua endpoint memerlukan header:
X-Access-Key: your-access-key

Endpoint yang memerlukan user authentication juga memerlukan:
Authorization: Bearer {token}

--------------------------------------------------------------------------------
ENDPOINTS - AUTHENTICATION
--------------------------------------------------------------------------------

Register User
POST /api/register
Headers:
  X-Access-Key: your-access-key
  Content-Type: application/json
Body:
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "phone": "081234567890",
  "address": "Jakarta Selatan"
}

Login
POST /api/login
Headers:
  X-Access-Key: your-access-key
  Content-Type: application/json
Body:
{
  "email": "john@example.com",
  "password": "password123"
}

Logout
POST /api/logout
Headers:
  X-Access-Key: your-access-key
  Authorization: Bearer {token}

--------------------------------------------------------------------------------
ENDPOINTS - PRODUCTS
--------------------------------------------------------------------------------

Get All Products
GET /api/products?per_page=10
Headers:
  X-Access-Key: your-access-key

Get Product Detail
GET /api/products/{id}
Headers:
  X-Access-Key: your-access-key

--------------------------------------------------------------------------------
ENDPOINTS - ORDERS
--------------------------------------------------------------------------------

Checkout (Create Order)
POST /api/checkout
Headers:
  X-Access-Key: your-access-key
  Authorization: Bearer {token}
  Content-Type: application/json
Body:
{
  "items": [
    {
      "product_id": 1,
      "quantity": 2
    }
  ],
  "shipping_address": "Jl. Sudirman No. 123, Jakarta"
}

Generate Payment URL
POST /api/orders/{orderId}/payment
Headers:
  X-Access-Key: your-access-key
  Authorization: Bearer {token}

Get Order History
GET /api/orders/history?per_page=10
Headers:
  X-Access-Key: your-access-key
  Authorization: Bearer {token}

--------------------------------------------------------------------------------
ENDPOINTS - WEBHOOK
--------------------------------------------------------------------------------

DOKU Payment Notification
POST /api/webhook/doku
(Called automatically by DOKU)

=============================================
TESTING
=============================================

Testing dengan Postman:
1. Import Postman collection
2. Set environment variables:
   - base_url: API base URL
   - access_key: Your access key
3. Run collection

Testing dengan cURL:

# Test get products
curl https://your-api-url.railway.app/api/products \
  -H "X-Access-Key: your-access-key"

# Test register
curl -X POST https://your-api-url.railway.app/api/register \
  -H "X-Access-Key: your-access-key" \
  -H "Content-Type: application/json" \
  -d '{"name":"Test","email":"test@example.com","password":"password123"}'

=============================================
PROJECT STRUCTURE
=============================================

ecommerce-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       ├── AuthController.php
│   │   │       ├── ProductController.php
│   │   │       ├── OrderController.php
│   │   │       └── WebhookController.php
│   │   └── Middleware/
│   │       └── CheckAccessKey.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Order.php
│   │   └── OrderItem.php
│   └── Services/
│       └── DokuService.php
├── database/
│   ├── migrations/
│   └── seeders/
│       └── ProductSeeder.php
├── routes/
│   └── api.php
├── Procfile
├── nixpacks.toml
└── README.md

=============================================
SECURITY FEATURES
=============================================

- Custom Access Key middleware untuk semua endpoint
- JWT token authentication dengan Laravel Sanctum
- CSRF protection
- Rate limiting
- SQL injection protection (Eloquent ORM)
- XSS protection
- HTTPS enforcement di production
- Webhook signature validation (DOKU)

=============================================
TROUBLESHOOTING
=============================================

Database Connection Error:
php artisan config:clear
php artisan cache:clear
php artisan tinker
>>> DB::connection()->getPdo();

Migration Error:
php artisan migrate:fresh --force
php artisan db:seed

500 Server Error:
railway logs
# Set APP_DEBUG=true (local only)

=============================================
ENVIRONMENT VARIABLES REFERENCE
=============================================

Variable            Description                  Example
--------------------------------------------------------------------------------
APP_KEY             Application encryption key   base64:xxx...
APP_URL             Application URL              https://api.example.com
DB_HOST             Database host                postgres.railway.internal
DB_DATABASE         Database name                railway
ACCESS_KEY          Custom API access key        secret-key-123
DOKU_CLIENT_ID      DOKU client ID              BRN-xxx
DOKU_SECRET_KEY     DOKU secret key             SK-xxx

=============================================
AUTHOR
=============================================

GitHub: @rizko-d
LinkedIn: Rizko Febri Rachmayadi
Portfolio: https://portfolio-rizko.vercel.app/

=============================================
SUPPORT
=============================================

Email: rizkofebry@gmail.com
GitHub Issues: https://github.com/your-username/ecommerce-api/issues

Made with ❤️ using Laravel & Railway
