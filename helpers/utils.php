<?php

use Illuminate\Support\Facades\Route;

function resolveAbility($ability)
{
    return null;
    if (!$ability) {
        return null;
    }
    return 'permission' . ':' . $ability;
}

/**
 * Create routes for GET, POST, PUT and DELETE methods
 * 
 * @param  array  $prefixes  Routes prefix name
 * @param  array  $controllers Controllers class name
 * 
 * @return  void
 */
function createRoute(array $prefixes, array $controllers)
{
    if (count($prefixes) == count($controllers)) {
        foreach ($prefixes as $key => $value) {
            Route::prefix($value)->group(function () use ($key, $value, $controllers) {
                $controler = $controllers[$key];
                $startAbility = $value;
                Route::get('/{id?}', [$controler, 'index'])->middleware(resolveAbility($startAbility . '-list'));
                Route::post('/', [$controler, 'store'])->middleware(resolveAbility($startAbility . '-create'));
                Route::put('/{id}', [$controler, 'update'])->middleware(resolveAbility($startAbility . '-update'));
                Route::delete('/{id}', [$controler, 'delete'])->middleware(resolveAbility($startAbility . '-delete'));
            });
        }
    } else
        throw new Exception('Prefixes array and controllers array must have same length.');
}
