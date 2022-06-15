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

Run `/api/login` with `POST` method to get token and set it to each request header. Your request body:
```
{
    username: "bob",
    password: "your_password"
}
```

`POST, GET, PUT, DELETE` methods are available for registered API routes. Once relationships are defined on your Eloquent model classes, make requests to your API in several ways. For example:
| Method | URI | Description |
| --- | --- | --- |
| POST | /api/example_route_prefix_name | Insert new record |
| GET | /api/example_route_prefix_name | Retreive all records |
| GET | /api/example_route_prefix_name/{id} | Retreive a record |
| PUT | /api/example_route_prefix_name/{id} | Update a record |
| DELETE | /api/example_route_prefix_name/{id} | Delete a record |

To insert/update a new record into a table at the same time as related table(s) into database, specify relationship(s) in request body.
```
{
    attribute1: value1,
    attribute2: value2
    ...
    my_one_to_one_relationship: {
        ...
    },
    ...
    my_one_to_many_relationship: [
        {...},
        {...}
    ]
}
```

Add these query parameters when making request to your API route with `GET` method.
| Query parameter | Description | Value |
| --- | --- | --- |
| `order` | ascending or descending order in which to sort the result set | `asc`, `desc` |
| `by` | the column to sort the result set by | `column name` |
| `where_column`,`whereLike_column`,`whereLike_relation__column` | extract only those records that fulfill a specified condition | eg: `where_lastname=bob`,`whereLike_adress=anov`,`whereLike_city__name=exico` |
| `orWhere_column`,`orWhereLike_column` | combine with above query parameters to filter the result set | eg: `orWhere_lastname=bob`,`orWhereLike_adress=anov` |
| `with_relation`,`with_relation1__relation2` | retrieve the result set with the relationships and those that are nested | eg: `with_customer`,`with_employees__department` |
| `withSum_column`,`withSum_relation_column` | retrieve the result set with total sum of a column | eg: `withSum_salary`,`withSum_transfers_amount` |
| `withCount_column` | retrieve the result set with number of records | eg: `withCount_id` |
