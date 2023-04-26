## Simple Laravel API

## Set up
Clone this repository, navigate to the repository root, create a ``.env`` from ``.env.example`` and run the following commands. Please make sure you set your database configuration in the ``.env``
file.
1. Install dependencies
```bash
    composer install
```
2. Run migration
```bash
    php artisan migrate
```
3. Seed Data
```bash
    php artisan db:seed
```
4. Start application
```bash
    php artisan serve
```
## Credentials

If you followed the steps above, you can login to any account with any of these emails **admin1@test.com**, **admin2@test.com**, **admin3@test.com** using **test** as password, else you will have to register and login with your credentials

```bash
    default password is test
```

## Endpoints
1. POST - http://localhost:8000/api/auth/login
2. POST - http://localhost:8000/api/admin/user - Registers admin user
3. PUT - http://localhost:8000/api/user/{userId} - Requests user update
4. DELETE - http://localhost:8000/api/user/{userId} - Requests user delete
5. POST - http://localhost:8000/api/user/register - Requests new user creation
6. POST - http://localhost:8000/api/user/requests/approve/{requestId} - Approved Requests
7. POST - http://localhost:8000/api/user/requests/decline/{requestId} - Decline Requests

## Required PHP version
^8.1
## Testing
Run the command below at the root directory of the project
```bash
    php artisan test
```
