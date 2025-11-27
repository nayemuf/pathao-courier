# Laravel Pathao Courier Package

A professional Laravel package for integrating with Pathao Courier Merchant API. This package provides a clean, well-structured interface for all Pathao API endpoints with built-in caching, rate limiting, and error handling.

## Features

- ✅ **OAuth 2.0 Authentication** with automatic token caching
- ✅ **Rate Limiting** to prevent API abuse
- ✅ **Comprehensive API Coverage** - All Pathao endpoints
- ✅ **Input Validation** before API calls
- ✅ **Error Handling** with detailed exception messages
- ✅ **Laravel Best Practices** - Service Provider, Facades, Config
- ✅ **Sandbox & Production** support
- ✅ **Type Safety** with proper type hints

## Installation

### For Local Development

1. The package is located in `packages/laravel-pathao-courier`
2. It's already configured in your `composer.json` as a local repository
3. Install it: `composer require laravel/pathao-courier:@dev`

### For Publishing (Future)

Once published to Packagist:

```bash
composer require nayemuf/pathao-courier
```

Then publish the config:

```bash
php artisan vendor:publish --tag=pathao-config
```

## Configuration

### Step 1: Publish the Config File

Publish the configuration file to your `config` directory:

```bash
php artisan vendor:publish --provider="Nayemuf\PathaoCourier\PathaoCourierServiceProvider" --tag=pathao-config
```

Or use the shorter command:

```bash
php artisan vendor:publish --tag=pathao-config
```

This will copy `packages/nayemuf-pathao-courier/config/pathao.php` to `config/pathao.php` in your Laravel application.

### Step 2: Configure Environment Variables

Add to your `.env`:

```env
PATHAO_SANDBOX=true
PATHAO_CLIENT_ID=your_client_id
PATHAO_CLIENT_SECRET=your_client_secret
PATHAO_USERNAME=your_email@example.com
PATHAO_PASSWORD=your_password
PATHAO_STORE_ID=your_store_id
```

### Sandbox Credentials (for testing)

```env
PATHAO_SANDBOX=true
PATHAO_CLIENT_ID=7N1aMJQbWm
PATHAO_CLIENT_SECRET=wRcaibZkUdSNz2EI9ZyuXLlNrnAv0TdPUPXMnD39
PATHAO_USERNAME=test@pathao.com
PATHAO_PASSWORD=lovePathao
```

### Test Store IDs (Sandbox)

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

Or visit: `http://your-domain.com/pathao-stores` (if you have the helper route configured)

## Usage

### Create an Order

```php
use Nayemuf\PathaoCourier\Facades\PathaoCourier;

$orderData = [
    'store_id' => 149043, // Use a test store ID from the list above
    'merchant_order_id' => 'ORD-12345',
    'recipient_name' => 'John Doe',
    'recipient_phone' => '01712345678',
    'recipient_address' => 'House 123, Road 4, Sector 10, Uttara, Dhaka-1230',
    'delivery_type' => 48, // 48 for normal, 12 for on-demand
    'item_type' => 2, // 1 for document, 2 for parcel
    'item_quantity' => 1,
    'item_weight' => '0.5',
    'amount_to_collect' => 1000, // 0 for non-COD
    'item_description' => 'Product description',
];

$response = PathaoCourier::order()->create($orderData);
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

### Calculate Price

```php
$priceData = [
    'store_id' => 149043, // Use a test store ID from the list above
    'item_type' => 2,
    'delivery_type' => 48,
    'item_weight' => 0.5,
    'recipient_city' => 1,
    'recipient_zone' => 298,
];

$price = PathaoCourier::price()->calculate($priceData);
```

### Get Store List

```php
$stores = PathaoCourier::store()->list();
```

### Get Store Info (Single Store)

```php
$storeInfo = PathaoCourier::store()->getInfo($storeId);
```

### Get Order Info

```php
$orderInfo = PathaoCourier::order()->getInfo($consignmentId);
$orderDetails = PathaoCourier::order()->getDetails($consignmentId);
```

### Refresh Access Token

```php
// Refresh access token using refresh token
$response = PathaoCourier::refreshToken($refreshToken);
// Returns: ['access_token' => '...', 'refresh_token' => '...', 'expires_in' => 432000]
```

### Create Bulk Orders

```php
$orders = [
    [
        'store_id' => 149043, // Use a test store ID from the list above
        'recipient_name' => 'John Doe',
        // ... other fields
    ],
    [
        'store_id' => 149043, // Use a test store ID from the list above
        'recipient_name' => 'Jane Doe',
        // ... other fields
    ],
];

$response = PathaoCourier::order()->createBulk($orders);
```

## API Methods

### Authentication

- `PathaoCourier::refreshToken(string $refreshToken)` - Refresh access token using refresh token

### OrderApi

- `create(array $orderData)` - Create a new order
- `createBulk(array $orders)` - Create multiple orders
- `getInfo(string $consignmentId)` - Get order short info
- `getDetails(string $consignmentId)` - Get full order details

### AreaApi

- `getCities()` - Get list of all cities
- `getZones(int $cityId)` - Get zones for a city
- `getAreas(int $zoneId)` - Get areas for a zone

### StoreApi

- `create(array $storeData)` - Create a new store
- `list()` - Get list of stores
- `getInfo(int $storeId)` - Get merchant store info (single store details)

### PriceApi

- `calculate(array $priceData)` - Calculate delivery price

## Error Handling

The package throws `Nayemuf\PathaoCourier\Exceptions\PathaoException` for all API errors:

```php
use Nayemuf\PathaoCourier\Exceptions\PathaoException;

try {
    $response = PathaoCourier::order()->create($orderData);
} catch (PathaoException $e) {
    // Handle error
    $message = $e->getMessage();
    $errors = $e->getErrors(); // Array of validation errors if any
    $code = $e->getCode(); // HTTP status code
}
```

## Caching

Access tokens are automatically cached to reduce API calls. Tokens are cached for their full lifetime (minus 5 minutes for safety).

## Rate Limiting

The package includes built-in rate limiting (60 requests per minute by default). This can be configured in the config file.

## Validation

All input data is validated before sending to the API:

- Recipient name: 3-100 characters
- Recipient phone: Exactly 11 characters
- Recipient address: 10-220 characters
- Item weight: 0.5-10 kg
- Delivery type: 48 (Normal) or 12 (On Demand)
- Item type: 1 (Document) or 2 (Parcel)

## Testing

```bash
composer test
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

MIT License

## Support

For issues and questions, please open an issue on GitHub.

