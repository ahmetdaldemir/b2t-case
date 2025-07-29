# B2B Order Management API

Laravel 12 ile geliştirilmiş B2B sipariş yönetim API'si. Bu proje, REST API geliştirme, kimlik doğrulama, rol tabanlı erişim, Eloquent ilişkileri, pivot tablo kullanımı ve önbellekleme becerilerini değerlendirmek için tasarlanmıştır.

## 🚀 Özellikler

- **Authentication**: Laravel Sanctum ile API kimlik doğrulama
- **Authorization**: Rol tabanlı erişim kontrolü (admin, customer)
- **Models**: User, Product, Order, OrderItem (pivot table)
- **API Endpoints**: CRUD işlemleri için REST API
- **Caching**: Redis ile önbellekleme
- **Docker**: Tam Docker desteği
- **Database**: MySQL 8.0
- **Testing**: Factory'ler ve test verileri

## 📋 Gereksinimler

- Docker
- Docker Compose
- Git

## 🛠️ Kurulum

### 1. Projeyi Klonlayın
```bash
git clone <repository-url>
cd b2t-case/proje
```

### 2. Docker Servislerini Başlatın
```bash
docker-compose up -d
```

### 3. Bağımlılıkları Yükleyin
```bash
docker-compose exec app composer install
```

### 4. Environment Dosyasını Yapılandırın
```bash
cp .env.example .env
```

### 5. Laravel Key'ini Generate Edin
```bash
docker-compose exec app php artisan key:generate
```

### 6. Veritabanı Migration'larını Çalıştırın
```bash
docker-compose exec app php artisan migrate:fresh --seed
```

## 🐳 Docker Servisleri

Proje aşağıdaki Docker servislerini içerir:

| Servis | Port | Açıklama |
|--------|------|----------|
| **Laravel API** | http://localhost:8000 | Ana API uygulaması |
| **phpMyAdmin** | http://localhost:8080 | Veritabanı yönetimi |
| **MySQL** | localhost:3308 | Veritabanı |
| **Redis** | localhost:6379 | Önbellekleme |

### phpMyAdmin Erişim Bilgileri
- **URL**: http://localhost:8080
- **Kullanıcı**: `b2c`
- **Şifre**: `b2c`
- **Veritabanı**: `b2cd`

## 📊 Veritabanı Yapısı

### Users Tablosu
- `id` - Primary Key
- `name` - Kullanıcı adı
- `email` - E-posta (unique)
- `password` - Şifre (hashed)
- `role` - Rol (admin/customer)
- `created_at`, `updated_at` - Timestamps

### Products Tablosu
- `id` - Primary Key
- `name` - Ürün adı
- `sku` - Stok kodu (unique)
- `price` - Fiyat (decimal)
- `stock_quantity` - Stok miktarı
- `created_at`, `updated_at` - Timestamps

### Orders Tablosu
- `id` - Primary Key
- `user_id` - Foreign Key (users)
- `status` - Durum (pending/approved/shipped)
- `total_price` - Toplam fiyat (decimal)
- `created_at`, `updated_at` - Timestamps

### Order_Items Tablosu (Pivot)
- `id` - Primary Key
- `order_id` - Foreign Key (orders)
- `product_id` - Foreign Key (products)
- `quantity` - Miktar
- `unit_price` - Birim fiyat (decimal)
- `created_at`, `updated_at` - Timestamps

## 🔐 Test Kullanıcıları

### Admin Kullanıcısı
- **Email**: `admin@b2b.com`
- **Password**: `password`
- **Rol**: `admin`

### Customer Kullanıcısı
- **Email**: `customer@b2b.com`
- **Password**: `password`
- **Rol**: `customer`

## 📡 API Endpoints

### Authentication Endpoints

#### Register
```http
POST /api/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password",
    "password_confirmation": "password"
}
```

#### Login
```http
POST /api/login
Content-Type: application/json

{
    "email": "customer@b2b.com",
    "password": "password"
}
```

#### Logout
```http
POST /api/logout
Authorization: Bearer {token}
```

### Product Endpoints

#### Get All Products (Cached)
```http
GET /api/products
Authorization: Bearer {token}
```

#### Create Product (Admin Only)
```http
POST /api/products
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "Sample Product",
    "sku": "SP001",
    "price": 99.99,
    "stock_quantity": 100
}
```

#### Update Product (Admin Only)
```http
PUT /api/products/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "Updated Product",
    "price": 149.99,
    "stock_quantity": 50
}
```

#### Delete Product (Admin Only)
```http
DELETE /api/products/{id}
Authorization: Bearer {token}
```

### Order Endpoints

#### Get Orders (Role-based)
```http
GET /api/orders
Authorization: Bearer {token}
```

#### Create Order (Customer Only)
```http
POST /api/orders
Authorization: Bearer {token}
Content-Type: application/json

{
    "products": [
        {
            "product_id": 1,
            "quantity": 2
        },
        {
            "product_id": 3,
            "quantity": 1
        }
    ]
}
```

#### Get Order Details
```http
GET /api/orders/{id}
Authorization: Bearer {token}
```

## 🔧 Komutlar

### Docker Komutları
```bash
# Servisleri başlat
docker-compose up -d

# Servisleri durdur
docker-compose down

# Logları görüntüle
docker-compose logs -f

# Belirli servisin loglarını görüntüle
docker-compose logs -f app
```

### Laravel Komutları
```bash
# Migration'ları çalıştır
docker-compose exec app php artisan migrate

# Migration'ları sıfırla ve seed'le
docker-compose exec app php artisan migrate:fresh --seed

# Cache'i temizle
docker-compose exec app php artisan cache:clear

# Config'i cache'le
docker-compose exec app php artisan config:cache

# Route'ları listele
docker-compose exec app php artisan route:list
```

### Test Verileri
```bash
# Test verilerini kontrol et
docker-compose exec app php artisan tinker --execute="echo 'Users: ' . App\Models\User::count();"
``` 

## 📄 Lisans

Bu proje MIT lisansı altında lisanslanmıştır.

---

**Geliştirici**: Ahmet DALDEMİR
**Versiyon**: 1.0.0
**Son Güncelleme**: 2025-07-29
