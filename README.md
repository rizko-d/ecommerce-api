
# E-COMMERCE API - LARAVEL

RESTful API untuk sistem e-commerce dengan fitur autentikasi, manajemen produk, checkout, dan integrasi payment gateway DOKU.



## Demo

Live API: https://web-production-e35b7.up.railway.app/


## Features

- User Authentication (Register, Login, Logout) dengan Laravel Sanctum
- Product Management (CRUD operations)
- Shopping Cart & Checkout System
- Order Management dengan invoice generation
- Payment Gateway Integration (DOKU)
- Webhook Notification untuk status pembayaran
- Custom Access Key Middleware untuk security
- Database Seeding untuk sample data
- API Documentation dengan Postman Collection
## Tech Stack

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


## REQUIREMENTS

- PHP >= 8.2
- Composer
- PostgreSQL >= 14
- Git
## Installation ( Local )

1. Clone Repository

```bash
git clone https://github.com/rizko-d/ecommerce-api.git
cd ecommerce-api
```

2. Install Dependencies
```bash
composer install
```

3. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```
4. Configure Database
Edit .env file:

```bash
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=ecommerce_local
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

5. Run Migrations & Seeders
```bash
php artisan migrate
php artisan db:seed
# Or run both at once
php artisan migrate:fresh --seed
```
6. Start Development Server
```bash
php artisan serve
```
API akan berjalan di: http://localhost:8000
## API Documentation

Base URL:
Production: https://ecommerce-api-production-f54e.up.railway.app
Local: http://localhost:8000

Authentication:
Semua endpoint memerlukan header:
```bash
X-Access-Key = your-access-key
```
Endpoint yang memerlukan user authentication juga memerlukan:
```bash
Authorization: Bearer {token}
```
## EndPoint Testing POSTMAN

### AUTHENTICATION
#### Register
```http
  POST /api/register
```

| Header |  Description                |
| :-------- |  :------------------------- |
| `X-Access-Key` | **Required**. your-access-key | 
`Content-Type`  | application/json

##### Body 
```bash 
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "phone": "081234567890",
  "address": "Indonesia"
}
```

#### Login

```http
  POST /api/login
```

| Header |  Description                       |
| :-------- |  :-------------------------------- |
|  `X-Access-Key`      |  **Required**. your-access-key |
`Authorization` | Bearer {token}

### PRODUCTS
#### Get All Products
```http
  GET /api/products?per_page=10
```
#### Get Products by ID 
```http
  GET /api/products/1
```

| Header |  Description                |
| :-------- |  :------------------------- |
| `X-Access-Key` | **Required**. your-access-key | 

### ORDERS
#### Checkout (Create Order)
```http
  POST /api/checkout
```
| Header |  Description                       |
| :-------- |  :-------------------------------- |
|  `X-Access-Key`      |  **Required**. your-access-key |
`Authorization` | Bearer {token}
`Content-Type`  | application/json

##### body 
```http
{
  "items": [
    {
      "product_id": 1,
      "quantity": 2
    }
  ],
  "shipping_address": "Jl. 123, Jakarta"
}
```

#### Generate Payment URL
```http
  POST /api/orders/{orderId}/payment
```

| Header |  Description                       |
| :-------- |  :-------------------------------- |
|  `X-Access-Key`      |  **Required**. your-access-key |
`Authorization` | Bearer {token}

#### Get Order History
```http
GET /api/orders/history?per_page=10
```
| Header |  Description                       |
| :-------- |  :-------------------------------- |
|  `X-Access-Key`      |  **Required**. your-access-key |
`Authorization` | Bearer {token}

#### WEBHOOK
DOKU Payment Notification
```http
POST /api/webhook/doku
```
(Called automatically by DOKU)


## Authors

- Github [@rizko-d](https://www.github.com/rizko-d)
- Linkedin [Rizko Febri Rachmayadi](https://www.linkedin.com/in/rizkofebri/)
- Portfolio [Rizko](https://www.linkedin.com/in/rizkofebri/)



## Support

Email: rizkofebry@gmail.com 

GitHub Issues: https://github.com/rizko-d/ecommerce-api/issues

Made with ❤️ using Laravel & Railway

