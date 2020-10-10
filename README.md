## How to deploy locally
- After clone install packages with _composer install_
- Copy file .env.example to .env and fill it with your local machine
- To create database structure, use _php artisan migrate_
- To serve your project locally, you may use the [Laravel Homestead](http://laravel.com/docs/homestead) virtual machine, [Laravel Valet](http://laravel.com/docs/valet), or the built-in PHP development server: `php -S localhost:8000 -t public`

## Testing
On project direcory you can run `vendor/bin/phpunit`.