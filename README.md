# Our custom Laravel tool to code very quickly an API.

## Prerequisites

* [PHP ^7.3|^8.0](https://www.php.net/downloads.php)
* [Laravel framework ^8.65](https://laravel.com/docs/8.x)

## Installation

Install packages with [Composer](https://getcomposer.org/).
Just run the following:
> composer install

### Database configuration

Edit database environment variables in `.env` file and run the following command:
> php artisan migrate

Use [migrations](https://laravel.com/docs/8.x/migrations) to define database schema.

### API authentication

Laravel Passport is an OAuth2 server and API authentication package that is simple and enjoyable to use. Run the following commands to install and configure the package:
```
php artisan passport:install
php artisan passport:client --personal
```
In prompt cmd type `bob` for name.

Then add following lines to `.env` file.
```
PASSPORT_PERSONAL_ACCESS_CLIENT_ID=3
PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET=tyQWXh7c6CsWj6p5jHEgxlfhyypOdKsIQIieicBh
```

> php artisan db:seed

## Quick Start and examples

Use [Eloquent](https://laravel.com/docs/8.x/eloquent) to create models corresponding to each database table.

Then, content of added controllers must be as follows:
```
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MyModel;

class ExampleController extends Controller
{
    public function __construct()
    {
        parent::__construct(new MyModel());
    }
}
```

Finally, open file `/routes/api.php` and use `createRoute()` function to register API routes.
```
createRoute(
        [
            'example_route_prefix_name',
            ...
        ],
        [
            ExampleController::class,
            ...
        ]
    );
```

## How to use
