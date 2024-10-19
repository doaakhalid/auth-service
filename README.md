# Auth Service

Auth Service is a Laravel-based authentication service designed to provide user authentication and authorization features for applications. This service is part of a larger project aimed at streamlining user management.

## Features

- User registration and login
- Password recovery
- Use Twilio to send otp message

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/doaakhalid/auth-service.git
   ```
2. Navigate to the directory:
   ```bash
   cd auth-service
   ```
3. Install dependencies:
   ```bash
   composer install
   ```
4. Set up the environment file:
   ```bash
   cp .env.example .env
   ```
5. Generate the application key:
   ```bash
   php artisan key:generate
   ```
6. Run the migrations:
   ```bash
   php artisan migrate
   ```

## Usage

To start the local development server:
```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## Testing APIs

To test the APIs, you can use tools like Postman or cURL. Here are some example API requests:

1. **User Registration**:
   - **Endpoint**: `POST /api/register`
   - **Body** (JSON):
     ```json
     {
       "name": "John Doe",
       "email": "johndoe@example.com",
       "password": "password123",
       "password_confirmation": "password123"
     }
     ```

2. **User Login**:
   - **Endpoint**: `POST /api/login`
   - **Body** (JSON):
     ```json
     {
       "email": "johndoe@example.com",
       "password": "password123"
     }
     ```

3. **Password Recovery**:
   - **Endpoint**: `POST /api/forgot-password`
   - **Body** (JSON):
     ```json
     {
       "phone": "011111111"
     }
     ```
3. **Verify otp**:
   - **Endpoint**: `POST /api/verify-otp`
   - **Body** (JSON):
     ```json
     {
       "phone": "011111111",
       "otp_code":"123456"
     }
     ```

