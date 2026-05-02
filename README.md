# TodoApp - Laravel + Vue 3 + InertiaJS

A modern todo application built with Laravel 11, Vue 3, InertiaJS, Tailwind CSS, and TypeScript.

## Tech Stack

- **Backend**: Laravel 11 (PHP 8.5)
- **Frontend**: Vue 3 + TypeScript
- **Routing**: InertiaJS
- **Styling**: Tailwind CSS
- **Database**: MySQL 8.4
- **Testing**: PHPUnit

## Features

- CRUD operations for Todo Items
- RESTful API with validation
- Server-side transactions
- Database constraints (NOT NULL)
- Toast notifications
- Responsive UI with Tailwind CSS
- Comprehensive unit & feature tests

## Project Structure

```
├── app/
│   ├── Http/Controllers/Api/TodoItemController.php  # API Controller
│   └── Models/TodoItem.php                           # Eloquent Model
├── database/
│   └── migrations/                                   # Database Migrations
├── resources/js/
│   ├── Pages/                                         # Inertia Pages
│   ├── Components/                                    # Vue Components
│   ├── composables/                                   # Vue Composables
│   └── types/                                         # TypeScript Interfaces
├── routes/
│   ├── web.php                                        # Web Routes (Inertia)
│   └── api.php                                        # API Routes
└── tests/
    ├── Unit/TodoItemTest.php                          # Unit Tests
    └── Feature/TodoItemApiTest.php                   # Feature Tests
```

## Database Schema

**Table: `todo_items`**

| Column      | Type        | Constraints       |
| ----------- | ----------- | ----------------- |
| id          | bigInteger  | Primary Key, Auto |
| name        | string(255) | NOT NULL          |
| description | text        | NOT NULL          |
| created_at  | timestamp   |                   |
| updated_at  | timestamp   |                   |

## API Endpoints

| Method | Endpoint               | Description     |
| ------ | ---------------------- | --------------- |
| GET    | `/api/todo-items`      | List all items  |
| POST   | `/api/todo-items`      | Create new item |
| GET    | `/api/todo-items/{id}` | Get single item |
| PUT    | `/api/todo-items/{id}` | Update item     |
| DELETE | `/api/todo-items/{id}` | Delete item     |

### Validation Rules

- **name**: required, string, max 255
- **description**: required, string

## Frontend Routes

| URL           | Page     | Description        |
| ------------- | -------- | ------------------ |
| `/`           | Home     | Welcome page       |
| `/todo-items` | TodoList | List all todos     |
| `/new`        | NewItem  | Create new todo    |
| `/edit/{id}`  | EditItem | Edit existing todo |

## Setup Instructions

### Prerequisites

- PHP 8.5+
- Node.js & npm
- Podman (or Docker)

### 1. Start MySQL Database

```bash
docker-compose up -d
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
php composer.phar install

# Install npm dependencies
npm install
```

### 3. Run Migrations

```bash
php artisan migrate
```

### 4. Build Frontend Assets

```bash
npm run build
```

### 5. Start Development Server

```bash
# Start Laravel server
php artisan serve

# Start Vite dev server (for hot reload)
npm run dev
```

## Running Tests

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature
```

### Test Coverage

**Unit Tests (5 tests):**

- Create todo item
- Fillable attributes
- Update todo item
- Delete todo item
- Items in descending order
- Database rejects null description

**Feature Tests (11 tests):**

- List all todo items
- Create todo item
- Validation: name required (create)
- Validation: name max length
- Show single item
- 404 for non-existent item
- Update todo item
- Validation: name required (update)
- Validation: description required (update)
- Delete todo item
- Create with description

## Environment Variables

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=password
```

## Deployment to Render

### Prerequisites

- [Render](https://render.com) account
- Git repository with your code

### Deployment Steps

#### Option 1: Deploy from GitHub (Recommended)

1. **Push your code to GitHub**
   ```bash
   git add .
   git commit -m "Prepare for deployment"
   git push origin main
   ```

2. **Create a new Web Service on Render**
   - Go to [Render Dashboard](https://dashboard.render.com)
   - Click "New" → "Web Service"
   - Connect your GitHub repository

3. **Configure the service**
   - Name: `todo-app`
   - Environment: `Docker`
   - Region: `Oregon` (or your preferred region)
   - Plan: `Free`

4. **Add Environment Variables**
   - `APP_ENV`: `production`
   - `APP_DEBUG`: `false`
   - `APP_KEY`: (generate with `php artisan key:generate`)

5. **Create a MySQL Database**
   - Go to "New" → "PostgreSQL" or "MySQL"
   - Name: `todo-mysql`
   - Plan: `Free`

6. **Deploy**
   - Click "Create Web Service"
   - Wait for the build to complete

#### Option 2: Deploy using render.yaml

1. **Push to GitHub**
   ```bash
   git add .
   git commit -m "Add render.yaml for deployment"
   git push origin main
   ```

2. **Import on Render**
   - Go to [Render Dashboard](https://dashboard.render.com)
   - Click "New" → "Import" → "render.yaml"
   - Connect your repository

3. **Deploy**
   - Render will automatically create the web service and MySQL database

### Important Notes

- **Generate APP_KEY** for production:
  ```bash
  php artisan key:generate --show
  ```

- **Run migrations** after deployment:
  - Render automatically runs `CMD` from Dockerfile, but you may need to use a post-deploy command or SSH to run `php artisan migrate`

- **Free tier limitations**:
  - Services sleep after 15 minutes of inactivity
  - MySQL free tier has limited storage
  - Automatic deployments on push to main branch

### Files for Deployment

- `Dockerfile` - Container configuration
- `render.yaml` - Render configuration (optional)
- `.dockerignore` - Exclude files from Docker build
- `.env.production` - Production environment template

## License

MIT License
