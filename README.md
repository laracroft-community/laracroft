# Our custom Laravel tool to code very quickly an API.

## Installation
Install packages with **Composer**.
Just run the following:
> composer install

### Database configuration
Run the following commands:
> php artisan migrate

### API authentication
Laravel Passport is an OAuth2 server and API authentication package that is simple and enjoyable to use. Run the following commands to install and configure the package:
```
php artisan passport:install
php artisan passport:client --personal
```
In prompt cmd type `bob` for name.

Then add these lines to `.env`.
```
PASSPORT_PERSONAL_ACCESS_CLIENT_ID=3
PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET=tyQWXh7c6CsWj6p5jHEgxlfhyypOdKsIQIieicBh
```

> php artisan db:seed

## Quick Start and Examples
