# WIP

This is still being developed. Its EAT is on Sunday (May 9)

#### Next changes:

- Develop todo search function
- Develop delete function
- Develop sort function
- Remove reorder function
- Remove third-part packages for repositories

---

# Overview

This is a simple application created on top of Laravel 8 and Vue 3.

Basically it was made only to show the Modular / Repository architecture
that I like, combined with some good practices, such as feature tests, 
requests for CRUD actions, API versioning, translation files, etc.

Please take into consideration that you may find functions that were not 
fully implemented, especially because this was not created to be a perfect project,
instead was used to show a very nice architecture with good packages and apply
good practices and patterns.

---

# Installation

1. Create a database in your server i.e. 
   `CREATE DATABASE laravel_vue_crud_example`
   
2. Configure your environment file: `.env`

3. Generate an API key: `php artisan key:generate`
   
4. Install the composer dependencies: `composer install`

5. Install the node dependencies: `npm install`

6. Run migrations: `php artisan migrate`
 
7. Seed the database with users: `php artisan db:seed`
   
8. Publish the front-end with `npm run dev`

9. Run the server with `php artisan serve`

The application API will be served on http://127.0.0.1:8000

> You can also run the front-end with `npm run serve` so the application will
> run in http://127.0.0.1:8080


