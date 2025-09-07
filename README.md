# 🛒 Choose Your Cart - Ecommerce Backend

A modern, feature-rich ecommerce backend API built with **Laravel 12** and **Filament v3 Admin Panel**. This project provides a complete backend solution for ecommerce applications with professional admin management capabilities.

![Laravel](https://img.shields.io/badge/Laravel-12.0-red.svg)
![Filament](https://img.shields.io/badge/Filament-3.3-orange.svg)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-blue.svg)
![License](https://img.shields.io/badge/License-MIT-green.svg)

## ✨ Features

### 🎯 **Core Ecommerce Features**
- **Product Management** with categories, pricing, and stock control
- **Advanced Sale System** with sale pricing and discount calculations
- **Order Management** with status tracking and payment verification
- **User Authentication** with Laravel Sanctum
- **Media Management** with Spatie Media Library
- **Inventory Tracking** with low stock alerts

### 📊 **Professional Admin Panel**
- **Modern Dashboard** with real-time analytics
- **Sales Charts** and revenue tracking
- **Stock Management** with quick restock actions
- **Bulk Operations** for efficient management
- **Advanced Filtering** and search capabilities
- **Responsive Design** optimized for all devices

### 🔌 **RESTful API**
- **Complete API endpoints** for all ecommerce operations
- **Authentication system** with API tokens
- **Pagination support** for large datasets
- **Search functionality** across products
- **Category-based filtering**
- **Sale price calculations** included in responses

## 🚀 Quick Start

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL 8.0 or higher
- Node.js & NPM

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/theanupambista/ecommerce-backend-cyc.git
   cd ecommerce-backend-cyc
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure Database**
   Edit your `.env` file with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. **Run Migrations & Seeders**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

7. **Build Frontend Assets**
   ```bash
   npm run build
   ```

8. **Start the Development Server**
   ```bash
   php artisan serve
   ```

## 🎯 Usage

### Admin Panel Access
- **URL**: `http://localhost:8000/admin`
- **Email**: `admin@chooseyourcart.com`
- **Password**: `33Uvus3]L@PT`

### API Base URL
```
http://localhost:8000/api/v1
```

## 📡 API Documentation

### Authentication Endpoints
```http
POST /api/v1/auth/register          # Register new user
POST /api/v1/auth/login             # User login
POST /api/v1/auth/logout            # User logout
GET  /api/v1/auth/profile           # Get user profile
```

### Product Endpoints
```http
GET    /api/v1/products             # Get all products
GET    /api/v1/products/featured    # Get featured products
GET    /api/v1/products/on-sale     # Get products on sale
GET    /api/v1/products/upcoming    # Get upcoming products
GET    /api/v1/products/search      # Search products
GET    /api/v1/products/{slug}      # Get single product
```

### Category Endpoints
```http
GET    /api/v1/categories           # Get all categories
GET    /api/v1/categories/{id}/products  # Get products by category
```

### Order Endpoints (Authentication Required)
```http
GET    /api/v1/orders               # Get user orders
POST   /api/v1/orders               # Create new order
GET    /api/v1/orders/{id}          # Get single order
POST   /api/v1/orders/{id}/cancel   # Cancel order
```

### Sample API Response
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Premium Smartphone",
    "slug": "premium-smartphone",
    "description": "Latest flagship smartphone...",
    "price": 89999.00,
    "sale_price": 79999.00,
    "is_on_sale": true,
    "current_price": 79999.00,
    "savings": 10000.00,
    "discount_percentage": 11,
    "stock": 50,
    "category": {
      "id": 1,
      "name": "Electronics",
      "slug": "electronics"
    },
    "image_url": "http://localhost:8000/storage/products/image.jpg"
  }
}
```

## 🏗️ Architecture

### Tech Stack
- **Backend**: Laravel 12 with PHP 8.2+
- **Admin Panel**: Filament v3
- **Frontend Assets**: Vite + Tailwind CSS v4
- **Database**: MySQL with Eloquent ORM
- **Authentication**: Laravel Sanctum
- **Media**: Spatie Media Library
- **API**: RESTful with JSON responses

### Project Structure
```
├── app/
│   ├── Filament/           # Admin panel resources & widgets
│   ├── Http/Controllers/   # API controllers
│   ├── Models/             # Eloquent models
│   └── Providers/          # Service providers
├── database/
│   ├── migrations/         # Database migrations
│   ├── seeders/           # Database seeders
│   └── factories/         # Model factories
├── routes/
│   ├── api.php            # API routes
│   └── web.php            # Web routes
└── resources/
    ├── views/             # Blade templates
    ├── css/               # Stylesheets
    └── js/                # JavaScript files
```

## 🎨 Admin Panel Features

### Dashboard Analytics
- **Sales Overview**: Real-time revenue and order statistics
- **Performance Charts**: 30-day sales trends
- **Stock Alerts**: Low inventory notifications
- **Top Products**: Best-selling items analysis

### Product Management
- **Rich Product Editor** with image upload
- **Automatic Slug Generation**
- **Sale Price Management** with discount calculations
- **Stock Control** with low stock alerts
- **Bulk Operations** for mass updates
- **Advanced Filtering** by category, status, stock level

### Order Management
- **Order Status Tracking**
- **Payment Verification System**
- **Customer Information Display**
- **Order History and Analytics**

## 🔧 Configuration

### Environment Variables
Key environment variables for configuration:

```env
# Application
APP_NAME="Choose Your Cart"
APP_ENV=production
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=your-database-host
DB_DATABASE=your-database-name
DB_USERNAME=your-username
DB_PASSWORD=your-password

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password

# File Storage
FILESYSTEM_DISK=public
```

## 🚀 Deployment

### Production Setup
1. **Server Requirements**
   - PHP 8.2+ with required extensions
   - MySQL 8.0+
   - Nginx or Apache
   - Composer
   - Node.js & NPM

2. **Deployment Steps**
   ```bash
   # Clone and setup
   git clone https://github.com/theanupambista/ecommerce-backend-cyc.git
   cd ecommerce-backend-cyc
   composer install --no-dev --optimize-autoloader
   npm install && npm run build
   
   # Configure environment
   cp .env.example .env
   # Edit .env with production settings
   php artisan key:generate
   
   # Database setup
   php artisan migrate --force
   php artisan db:seed --force
   
   # Optimize for production
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👨‍💻 Developer

**Built with ❤️ by [Yubraj Pandeya](https://github.com/yubrajpandeya)**

- **GitHub**: [@yubrajpandeya](https://github.com/yubrajpandeya)
- **Email**: [Contact Developer](mailto:yubrajpandeya@example.com)

## 🙏 Acknowledgments

- [Laravel](https://laravel.com) - The PHP Framework for Web Artisans
- [Filament](https://filamentphp.com) - Beautiful Admin Panel for Laravel
- [Tailwind CSS](https://tailwindcss.com) - Utility-first CSS Framework
- [Spatie Media Library](https://spatie.be/docs/laravel-medialibrary) - Media Management

## 📞 Support

If you encounter any issues or have questions:

1. Check the [Issues](https://github.com/theanupambista/ecommerce-backend-cyc/issues) page
2. Create a new issue if needed
3. Contact the developer

---

**⭐ Star this repository if you found it helpful!**
