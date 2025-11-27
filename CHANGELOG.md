# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2024-11-27

### Added

#### Core Features
- Complete OAuth 2.0 authentication system with automatic token management
- Token caching mechanism using Laravel Cache (5-day TTL with 5-minute safety buffer)
- Built-in rate limiting system (configurable, default: 60 requests/minute)
- Support for both sandbox and production environments
- Laravel Service Provider with automatic registration
- Facade support for easy access (`PathaoCourier::`)
- Publishable configuration file via `php artisan vendor:publish --tag=pathao-config`

#### API Implementations

**Authentication API:**
- Issue access token from credentials
- Refresh access token from refresh token

**Order API:**
- Create single order (`order()->create()`)
- Create bulk orders (`order()->createBulk()`)
- Get order short info (`order()->getInfo()`)
- Get order full details (`order()->getDetails()`)

**Area API:**
- Get list of all cities (`area()->getCities()`)
- Get zones for a city (`area()->getZones()`)
- Get areas for a zone (`area()->getAreas()`)

**Store API:**
- Create new store (`store()->create()`)
- Get list of all stores (`store()->list()`)
- Get single store information (`store()->getInfo()`)

**Price API:**
- Calculate delivery price (`price()->calculate()`)

#### Validation & Error Handling
- Comprehensive input validation for all API endpoints
- Custom `PathaoException` class with detailed error messages
- Field-level validation error reporting
- HTTP status code preservation in exceptions
- Validation rules for:
  - Recipient name (3-100 characters)
  - Recipient phone (exactly 11 characters, starts with 01)
  - Recipient address (10-220 characters)
  - Item weight (0.5-10 kg)
  - Delivery type (48 for normal, 12 for on-demand)
  - Item type (1 for document, 2 for parcel)

#### Documentation
- Comprehensive README with installation and usage examples
- API reference documentation
- Sandbox credentials and test store IDs
- Error handling examples
- Configuration guide
- Helper documentation for getting store IDs

#### Configuration
- Environment variable support for all settings
- Configurable cache prefix and TTL
- Configurable rate limiting (enable/disable and requests per minute)
- Sandbox mode toggle
- Store ID configuration

#### Code Quality
- Full type hints throughout the codebase
- Comprehensive PHPDoc blocks
- PSR-4 autoloading
- Laravel package standards compliance
- MIT License

### Technical Details

#### Dependencies
- PHP >= 8.2
- Laravel >= 10.0 (supports 10.x, 11.x, and 12.x)
- Guzzle HTTP Client >= 7.0
- Illuminate Support >= 10.0
- Illuminate Cache >= 10.0

#### Architecture
- Modular API class structure (BaseApi, OrderApi, AreaApi, StoreApi, PriceApi)
- Dependency injection ready
- Singleton pattern for API instances
- Separation of concerns (authentication, rate limiting, HTTP requests)

#### Logging
- Request/response logging for debugging
- Special logging for order creation responses
- Error logging with full context

---

## [Unreleased]

### Planned Features
- Webhook support for order status updates
- Order cancellation endpoint
- Batch order status checking
- Additional validation rules
- Unit tests coverage
- Integration tests
- CI/CD pipeline

---

[1.0.0]: https://github.com/nayemuf/pathao-courier/releases/tag/v1.0.0
[Unreleased]: https://github.com/nayemuf/pathao-courier/compare/v1.0.0...HEAD

