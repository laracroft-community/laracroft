<?php

namespace App\Http\Controllers\Traits;

trait ModelRequest
{
    /**
     * @param array $fillable
     * @param Request|array $inputs
     */
    public function getInputs($fillable, $inputs)
    {
        $response = [];
        $inputs = is_array($inputs) ? $inputs : $inputs->all();
        foreach ($fillable as $attribute) {
            foreach ($inputs as $key => $input) {
                if ($attribute == $key) {
                    $response[$key] = $input;
                }
            }
        }
        return $response;
    }

    /**
     * @param array $fillable
     * @param Illuminate\Http\Request|array $inputs
     * @param string $primaryKey
     */
    public function getUpdateInputs($fillable, $inputs, $primaryKey)
    {
        $response = [];
        $inputs = is_array($inputs) ? $inputs : $inputs->all();
        foreach ($fillable as $attribute) {
            foreach ($inputs as $key => $input) {
                if ($attribute == $key || $key == $primaryKey) {
                    $response[$key] = $input;
                }
            }
        }
        return $response;
    }

    /**
     * @param array $relation_methods
     * @param Illuminate\Http\Request|array $inputs
     */
    private function getRelations($relation_methods, $inputs)
    {
        $response = [];
        $inputs = is_array($inputs) ? $inputs : $inputs->all();
        foreach ($relation_methods as $relation_method) {
            foreach ($inputs as $key => $input) {
                if ($relation_method == $key) {
                    $response[] = [$relation_method => $input];
                }
            }
        }
        return $response;
    }

    /**
     * @param array $inputs
     * @param string $primaryKey
     * @param Illuminate\Database\Eloquent\Model $model
     */
    public function getDeletedIds($inputs, $primaryKey, $model)
    {
        $ids = [];
        foreach ($inputs as $value) {
            if (array_key_exists($primaryKey, $value)) {
                $ids[] = $value[$primaryKey];
            }
        }

        return $model::whereNotIn($primaryKey, $ids)->get()->pluck($primaryKey)->all();
    }
}