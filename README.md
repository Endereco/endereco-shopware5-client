# Endereco Shopware 5 Client

Endereco's Address Management Service plugin for Shopware 5, providing address validation, autocomplete, email verification, and more for improved customer data quality.

## Requirements

- Shopware 5.3.0 or higher
- PHP 7.4 or higher
- Composer (for development)
- Node.js and npm (for development)

## Development Setup

### Prerequisites

- PHP 7.4+
- Composer
- Docker (for testing)
- Node.js and npm
- Git

### Installation

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd endereco-shopware5-client
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies:**
   ```bash
   npm install
   ```

4. **Download Shopware versions for testing:**
   ```bash
   ./fetch-shops.sh
   ```

5. **Build frontend assets:**
   ```bash
   npm run build
   ```

### Testing

#### Manual Testing

Test the plugin with different Shopware 5 versions using:

```bash
./playground.sh
```

This will start a Dockware container with your chosen Shopware version and automatically install the plugin.

#### Code Quality

Run quality checks before committing:

```bash
# Run all quality checks
composer qa

# Individual checks
composer phpcs    # Code style check
composer phpcbf   # Fix code style
composer phpstan  # Static analysis
composer phpmd    # Mess detection
```

### Building for Distribution

```bash
./build-shopware5-client.sh
```

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct and the process for submitting pull requests.

## License

This project is licensed under the AGPLv3 License.
