# Secretlab Assessment

# Installation & Usage

# Install dependencies

```bash
composer install

# Setup environment

cp .env.example .env
php artisan key:generate

# Configure database in .env file

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kv_store
DB_USERNAME=root
DB_PASSWORD=secret

# Run migrations

php artisan migrate

# Start server

php artisan serve

```

## API Endpoints

### Store Key-Value Pairs

- **POST** `/api/object`
- **Body**: `{"key1": "value1", "key2": {"nested": "object"}}`

### Get Latest Value

- **GET** `/api/object/{key}`

### Get Value at Timestamp

- **GET** `/api/object/{key}?timestamp=1640000000`

### Get All Records

- **GET** `/api/object/get_all_records`

## Testing

```bash
# Run all tests
php artisan test
```
