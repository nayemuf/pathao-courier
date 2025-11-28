# Laravel Pathao Courier Package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/nayemuf/pathao-courier.svg?style=flat-square)](https://packagist.org/packages/nayemuf/pathao-courier)
[![Total Downloads](https://img.shields.io/packagist/dt/nayemuf/pathao-courier.svg?style=flat-square)](https://packagist.org/packages/nayemuf/pathao-courier)
[![License](https://img.shields.io/packagist/l/nayemuf/pathao-courier.svg?style=flat-square)](https://packagist.org/packages/nayemuf/pathao-courier)
[![Laravel](https://img.shields.io/badge/Laravel-10.x%20%7C%2011.x%20%7C%2012.x-orange.svg?style=flat-square)](https://laravel.com)

A professional Laravel package for integrating with **Pathao Courier Merchant API**. This package provides a clean, well-structured interface for all Pathao API endpoints with built-in caching, rate limiting, and comprehensive error handling.

## ✨ Features

- ✅ **Complete API Coverage** - All 11 Pathao Merchant API endpoints implemented
- ✅ **OAuth 2.0 Authentication** - Automatic token management with intelligent caching
- ✅ **Rate Limiting** - Built-in protection against API abuse (configurable)
- ✅ **Input Validation** - Comprehensive validation before API calls
- ✅ **Error Handling** - Detailed exception messages with field-level errors
- ✅ **Laravel Best Practices** - Service Provider, Facades, and publishable config
- ✅ **Sandbox & Production** - Full support for both environments
- ✅ **Type Safety** - Complete type hints and PHPDoc documentation
- ✅ **Zero Configuration** - Works out of the box with sensible defaults

## 📋 Requirements

- PHP >= 8.2
- Laravel >= 10.0
- Guzzle HTTP Client >= 7.0

## 📦 Installation

Install the package via Composer:

```bash
composer require nayemuf/pathao-courier
```

The package will automatically register its service provider and facade.

## ⚙️ Configuration

### Step 1: Publish Configuration

Publish the configuration file to your `config` directory:

```bash
php artisan vendor:publish --tag=pathao-config
```

This will create `config/pathao.php` in your Laravel application.

### Step 2: Environment Variables

Add the following to your `.env` file:

```env
PATHAO_SANDBOX=true
PATHAO_CLIENT_ID=your_client_id
PATHAO_CLIENT_SECRET=your_client_secret
PATHAO_USERNAME=your_email@example.com
PATHAO_PASSWORD=your_password
PATHAO_STORE_ID=your_store_id

# Optional: Rate Limiting
PATHAO_RATE_LIMIT_ENABLED=true
PATHAO_RATE_LIMIT_PER_MINUTE=60
```

### 🧪 Sandbox Credentials (for Testing)

Pathao provides sandbox credentials for testing:

```env
PATHAO_SANDBOX=true
PATHAO_CLIENT_ID=7N1aMJQbWm
PATHAO_CLIENT_SECRET=wRcaibZkUdSNz2EI9ZyuXLlNrnAv0TdPUPXMnD39
PATHAO_USERNAME=test@pathao.com
PATHAO_PASSWORD=lovePathao
```

### 📍 Test Store IDs (Sandbox)

You can use any of these store IDs for testing in the sandbox environment:

| Store ID | Store Name |
|----------|------------|
| `149049` | double discount |
| `149048` | Test Marchent |
| `149047` | Test Seller |
| `149046` | mo |
| `149045` | DCC Online |
| `149044` | Becho |
| `149043` | Sandbox Store |
| `149042` | Elvis Lowe |
| `149040` | Partha |
| `149039` | Partha Store |

**Note:** To get the full list of available stores, use:

```php
$stores = PathaoCourier::store()->list();
```

## 🚀 Usage

### Create an Order

```php
use Nayemuf\PathaoCourier\Facades\PathaoCourier;

$orderData = [
    'store_id' => 149043, // Your store ID
    'merchant_order_id' => 'ORD-12345', // Your internal order ID
    'recipient_name' => 'John Doe',
    'recipient_phone' => '01712345678', // 11 digits, starts with 01
    'recipient_address' => 'House 123, Road 4, Sector 10, Uttara, Dhaka-1230',
    'delivery_type' => 48, // 48 for normal, 12 for on-demand
    'item_type' => 2, // 1 for document, 2 for parcel
    'item_quantity' => 1,
    'item_weight' => '0.5', // in kg (0.5 to 10)
    'amount_to_collect' => 1000, // 0 for non-COD orders
    'item_description' => 'Product description', // Optional
];

try {
    $response = PathaoCourier::order()->create($orderData);
    // Response contains: consignment_id, invoice_id, etc.
} catch (\Nayemuf\PathaoCourier\Exceptions\PathaoException $e) {
    // Handle error
    logger()->error('Pathao order creation failed', [
        'message' => $e->getMessage(),
        'errors' => $e->getErrors(),
    ]);
}
```

### Create Bulk Orders

```php
$orders = [
    [
        'store_id' => 149043,
        'merchant_order_id' => 'ORD-001',
        'recipient_name' => 'John Doe',
        'recipient_phone' => '01712345678',
        'recipient_address' => 'Address 1',
        'delivery_type' => 48,
        'item_type' => 2,
        'item_quantity' => 1,
        'item_weight' => '0.5',
        'amount_to_collect' => 1000,
    ],
    [
        'store_id' => 149043,
        'merchant_order_id' => 'ORD-002',
        'recipient_name' => 'Jane Doe',
        'recipient_phone' => '01712345679',
        'recipient_address' => 'Address 2',
        'delivery_type' => 48,
        'item_type' => 2,
        'item_quantity' => 1,
        'item_weight' => '1.0',
        'amount_to_collect' => 0,
    ],
];

$response = PathaoCourier::order()->createBulk($orders);
```

### Get Order Information

```php
// Get order short info
$orderInfo = PathaoCourier::order()->getInfo($consignmentId);

// Get full order details
$orderDetails = PathaoCourier::order()->getDetails($consignmentId);
```

### Get Cities, Zones, and Areas

```php
// Get all cities
$cities = PathaoCourier::area()->getCities();

// Get zones for a city
$zones = PathaoCourier::area()->getZones($cityId);

// Get areas for a zone
$areas = PathaoCourier::area()->getAreas($zoneId);
```

### Calculate Delivery Price

```php
$priceData = [
    'store_id' => 149043,
    'item_type' => 2, // 1 for document, 2 for parcel
    'delivery_type' => 48, // 48 for normal, 12 for on-demand
    'item_weight' => 0.5, // in kg
    'recipient_city' => 1, // City ID
    'recipient_zone' => 298, // Zone ID
];

$price = PathaoCourier::price()->calculate($priceData);
// Returns: price, distance, etc.
```

### Store Management

```php
// Get list of all stores
$stores = PathaoCourier::store()->list();

// Get single store info
$storeInfo = PathaoCourier::store()->getInfo($storeId);

// Create a new store
$storeData = [
    'name' => 'My Store',
    'contact_name' => 'John Doe',
    'contact_number' => '01712345678',
    'address' => 'Store Address',
    'city_id' => 1,
    'zone_id' => 298,
    'area_id' => 1234,
];
$newStore = PathaoCourier::store()->create($storeData);
```

### Refresh Access Token

```php
// Refresh access token using refresh token
$response = PathaoCourier::refreshToken($refreshToken);
// Returns: ['access_token' => '...', 'refresh_token' => '...', 'expires_in' => 432000]
```

## 📚 API Reference

### Authentication

- `PathaoCourier::refreshToken(string $refreshToken)` - Refresh access token using refresh token

### Order API

- `PathaoCourier::order()->create(array $orderData)` - Create a new order
- `PathaoCourier::order()->createBulk(array $orders)` - Create multiple orders at once
- `PathaoCourier::order()->getInfo(string $consignmentId)` - Get order short info
- `PathaoCourier::order()->getDetails(string $consignmentId)` - Get full order details

### Area API

- `PathaoCourier::area()->getCities()` - Get list of all cities
- `PathaoCourier::area()->getZones(int $cityId)` - Get zones for a specific city
- `PathaoCourier::area()->getAreas(int $zoneId)` - Get areas for a specific zone

### Store API

- `PathaoCourier::store()->create(array $storeData)` - Create a new store
- `PathaoCourier::store()->list()` - Get list of all stores
- `PathaoCourier::store()->getInfo(int $storeId)` - Get merchant store info

### Price API

- `PathaoCourier::price()->calculate(array $priceData)` - Calculate delivery price

## ⚠️ Error Handling

The package throws `Nayemuf\PathaoCourier\Exceptions\PathaoException` for all API errors:

```php
use Nayemuf\PathaoCourier\Exceptions\PathaoException;

try {
    $response = PathaoCourier::order()->create($orderData);
} catch (PathaoException $e) {
    // Get error message
    $message = $e->getMessage();
    
    // Get field-level validation errors (if any)
    $errors = $e->getErrors(); // Array of validation errors
    
    // Get HTTP status code
    $code = $e->getCode();
    
    // Log or handle error
    logger()->error('Pathao API Error', [
        'message' => $message,
        'errors' => $errors,
        'code' => $code,
    ]);
}
```

## 🔒 Caching

Access tokens are automatically cached using Laravel's cache system to reduce API calls. Tokens are cached for their full lifetime (5 days) minus 5 minutes for safety. The cache key is configurable in `config/pathao.php`.

## 🚦 Rate Limiting

The package includes built-in rate limiting to prevent API abuse. By default, it limits requests to 60 per minute. You can configure this in your `config/pathao.php`:

```php
'rate_limit' => [
    'enabled' => true,
    'requests_per_minute' => 60,
],
```

## ✅ Validation Rules

All input data is validated before sending to the API:

- **Recipient name**: 3-100 characters
- **Recipient phone**: Exactly 11 characters (must start with 01)
- **Recipient address**: 10-220 characters
- **Item weight**: 0.5-10 kg
- **Delivery type**: 48 (Normal) or 12 (On Demand)
- **Item type**: 1 (Document) or 2 (Parcel)
- **Item quantity**: Minimum 1

## 🧪 Testing

Run the test suite:

```bash
composer test
```

Or run PHPUnit directly:

```bash
./vendor/bin/phpunit
```

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

### Guidelines

Before contributing, please ensure you follow these guidelines:

1. **Follow PSR-4 Autoloading Standard** - All code must adhere to [PSR-4](https://www.php-fig.org/psr/psr-4/) autoloading standards
2. **Refer to Pathao Official Documentation First** - Always check the [Pathao API Documentation](https://developer.pathao.com/) before implementing new features or changes
3. **Maintain Code Quality** - Follow existing code style, add proper type hints, and include PHPDoc comments
4. **Write Tests** - Include tests for new features or bug fixes
5. **Update Documentation** - Update README, CHANGELOG, and inline documentation as needed

### Contribution Process

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes following the guidelines above
4. Commit your changes (`git commit -m 'Add some amazing feature'`)
5. Push to the branch (`git push origin feature/amazing-feature`)
6. Open a Pull Request with a clear description of your changes

## 📝 Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## 🔗 Links

- [Packagist](https://packagist.org/packages/nayemuf/pathao-courier)
- [GitHub Repository](https://github.com/nayemuf/pathao-courier)
- [Pathao API Documentation](https://developer.pathao.com/)

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## 👤 Author

**Nayem Uddin**

- Email: nayem110899@gmail.com
- GitHub: [@nayemuf](https://github.com/nayemuf)
- LinkedIn: [@nayemuf](https://linkedin.com/in/nayemuf/)

## 🙏 Acknowledgments

- Pathao for providing the Merchant API
- Laravel community for the amazing framework

## 🐛 Reporting Issues

For issues, questions, or feature requests, please open an issue on [GitHub](https://github.com/nayemuf/pathao-courier/issues).

## 💝 Support

If this package helps you, please consider giving it a ⭐ on [Packagist](https://packagist.org/packages/nayemuf/pathao-courier) or [GitHub](https://github.com/nayemuf/pathao-courier).

---
