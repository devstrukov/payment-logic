# Payment Service API

REST API for card payments with Yii2, MariaDB, Redis, RabbitMQ. Event-driven architecture with DI container.

## 🚀 Quick Start

```bash
# Install
composer install
cp .env.example .env
./yii migrate/up

# Run
php -S localhost:8080 -t web

# Queues
./yii queue/process-all
```

## 🔐 Authentication

```bash
# Login
curl -X POST http://localhost:8080/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "user@example.com", "password": "password123"}'
```

## 📋 API Endpoints
```
Method	Endpoint	Description
GET	/payments/balance	User balance
POST	/payments/create	Create payment
GET	/payments/{id}/status	Payment status
```

## 💾 Database
```sql
users (id, email, token, balance, password_hash)
payments (id, user_id, amount, status)
attempts (id, payment_id, integration_id, status, external_id)
integrations (id, title, is_active)
```

## 🔧 Configuration
### .env
```bash
DB_DSN=mysql:host=localhost;dbname=payments
REDIS_HOST=localhost
YOO_MONEY_API_KEY=your_key
```