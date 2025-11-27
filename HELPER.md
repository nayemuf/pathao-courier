# Helper Guide: Getting Your Pathao Store ID

## Problem
If you're getting the error: **"Wrong Store selected"** or **"store_id: Wrong Store selected"**, it means the `PATHAO_STORE_ID` in your `.env` file is incorrect.

## Solution: Get Your Store ID

### Method 1: Using Tinker (Recommended)

Run this command in your terminal:

```bash
php artisan tinker
```

Then execute:

```php
use Nayemuf\PathaoCourier\Facades\PathaoCourier;

// Get list of your stores
$stores = PathaoCourier::store()->list();

// Display stores
print_r($stores);

// Or get just the store IDs
foreach ($stores['data']['data'] ?? [] as $store) {
    echo "Store ID: {$store['store_id']} - {$store['store_name']}\n";
}
```

### Method 2: Create a Temporary Route

Add this to `routes/web.php` temporarily:

```php
Route::get('/pathao-stores', function () {
    try {
        $stores = \Nayemuf\PathaoCourier\Facades\PathaoCourier::store()->list();
        return response()->json($stores, 200, [], JSON_PRETTY_PRINT);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
})->middleware('auth'); // Add authentication if needed
```

Then visit: `http://your-app.test/pathao-stores`

### Method 3: Check Pathao Dashboard

1. Log in to your Pathao Merchant Dashboard
2. Go to Store Settings
3. Find your Store ID there

## After Getting Store ID

1. Update your `.env` file:
   ```env
   PATHAO_STORE_ID=your_actual_store_id_here
   ```

2. Clear config cache:
   ```bash
   php artisan config:clear
   ```

3. Try creating the shipment again

## Common Issues

- **Store ID is a string**: Make sure it's a numeric value (e.g., `123`, not `"123"`)
- **Store not approved**: New stores need to wait 1 hour for approval
- **Wrong account**: Make sure you're using the store ID from the account you're authenticated with

