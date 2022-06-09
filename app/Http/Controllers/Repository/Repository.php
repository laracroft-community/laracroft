<?php

namespace App\Http\Controllers\Repository;

use App\Http\Controllers\Traits\ApiResponse;
use App\Http\Controllers\Traits\ModelRequest;
use App\Models\Contracts\IModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class Repository implements IRepository
{
    use ApiResponse, ModelRequest;

    /**
     * @var Model
     */
    private $model;

    /**
     * @param string|null $model
     */
    public function __construct($model = null)
    {
        if ($model) {
            $this->model = new $model;
        }
    }

    /**
     * Set new instance of model
     * 
     * @param string $model
     */
    public function setModel($model)
    {
        $this->model = new $model;
        return $this;
    }

    /**
     * get cuurent model instance
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * get current model rules
     */
    public function rules()
    {
        return $this->model->rules();
    }

    /**
     * get current model update_rules
     */
    public function update_rules()
    {
        return $this->model->update_rules();
    }

    /**
     * apply request query filters
     * 
     * ordering order=asc/desc by=column
     * 
     * where_column 
     * 
     * whereLike_column / whereLike_relation__column
     * 
     * orWhere_column
     * 
     * orWhereLike_column
     * 
     * with_relation or with_relation1__relation2
     * 
     * withCount_relation
     *  
     * @param Request $request
     * @return Builder $query
     */
    public function parse_filters(Request $request)
    {
        $query = $this->model::query();

        // order
        if ($request->has('by') && $request->get('by') && $request->has('order')) {
            if ($request->get('order') === 'asc' || $request->get('order') === 'desc') {
                $query->orderBy($request->get('by'), $request->get('order'));
            }
        }

        $withArray = [];
        foreach ($request->all() as $key1 => $value) {
            $where = str_contains($key1, 'where_') ?  explode('where_', $key1) : false;
            $whereRelation = str_contains($key1, 'whereRelation_') ?  explode('whereRelation_', $key1) : false;
            $whereLike = str_contains($key1, 'whereLike_') ?  explode('whereLike_', $key1) : false;
            $orWhere = str_contains($key1, 'orWhere_') ?  explode('orWhere_', $key1) : false;
            $orWhereLike = str_contains($key1, 'orWhereLike_') ?  explode('orWhereLike_', $key1) : false;
            $with = str_contains($key1, 'with_') ? explode('with_', $key1) : false;
            $withCount = str_contains($key1, 'withCount_') ? explode('withCount_', $key1) : false;
            $withSum = str_contains($key1, 'withSum_') ? explode('withSum_', $key1) : false;

            // where
            if ($where !== false && $value) {
                $query->where($where[1], $value);
                // dump('where '.$where[1].' '.$value);
            }

            // whereRelation
            if ($whereRelation !== false && $value) {
                $whereRelation = explode('_', $whereRelation[1]);
                $query->whereRelation($whereRelation[0], $whereRelation[1], $value);
            }

            // whereLike
            if ($whereLike !== false && $value) {
                $whereLikeArray = explode('__', $whereLike[1]);
                if (count($whereLikeArray) === 1) {
                    $query->where($whereLike[1], 'like', '%' . $value . '%');
                } else {
                    $relation = '';
                    $column = end($whereLikeArray);
                    if (count($whereLikeArray) === 2) {
                        $relation = $whereLikeArray[0];
                    } else {
                        array_pop($whereLikeArray);
                        foreach ($whereLikeArray as $key2 => $value) {
                            $relation .= $key2 === 0 ? $value : '.' . $value;
                        }
                    }
                    $query->whereHas($relation, function (Builder $subQuery) use ($column, $value) {
                        $subQuery->where($column, 'like', '%' . $value . '%');
                    });
                }
            }

            // orWhere
            if ($orWhere !== false && $value) {
                $query->orWhere($orWhere[1], $value);
            }

            // orWhereLike
            if ($orWhere !== false && $value) {
                $query->orWhere($orWhereLike[1], 'like', '%' . $value . '%');
            }

            // with
            if ($with !== false) {
                $withData = explode('__', $with[1]);

                if ($withData !== false) {
                    $withElement = '';
                    foreach ($withData as $key => $withDataValue) {
                        if ($key === 0) {
                            $withElement = $withElement . $withDataValue;
                        } else {
                            $withElement = $withElement . '.' . $withDataValue;
                        }
                    }
                    if ($withElement) {
                        $withArray[count($withArray)] = $withElement;
                    }
                }
            }

            // withCount
            if ($withCount !== false) {
                $query->withCount($withCount[1]);
            }

            // withSum
            if ($withSum !== false) {
                $withData = explode('_', $withSum[1]);
                if ($withData !== false)
                    $query->withSum($withData[0], $withData[1]);
                else 
                    $query->withSum($withSum[1]);
            }
        }

        $query->with($withArray);
        return $query;
    }

    /**
     * Store resource
     * 
     * @param Request|array $request
     */
    public function store($request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $inputs = $this->getInputs($this->model->getFillable(), $request);
                $relations = $this->getRelations($this->model->relation_methods, $request);

                $data = $this->model::create($inputs);
                foreach ($relations as $relation) {
                    $relationKey = array_key_first($relation);
                    $relationData = $relation[$relationKey];

                    /**
                     * @var Model $relationModel
                     */
                    $relationModel = $this->model->{$relationKey}()->getModel();

                    if (count($relationData) == count($relationData, COUNT_RECURSIVE)) {
                        // hasone type relation
                        $relationModel->create(
                            array_merge(
                                $this->getInputs($relationModel->getFillable(), $relationData),
                                [$this->model->getMigrateKey() => $data->getKey()]
                            )
                        );
                    } else {
                        // hasmany type relation
                        foreach ($relationData as $relationValue) {
                            $relationModel->create(
                                array_merge(
                                    $this->getInputs($relationModel->getFillable(), $relationValue),
                                    [$this->model->getMigrateKey() => $data->getKey()]
                                )
                            );
                        }
                    }
                }
                return $this->respondOk($data);
            });
        } catch (\Exception $e) {
            return $this->respondError($e);
        }
    }

    /**
     * Update resource
     *  
     * @param Request $request
     * @param int $id
     */
    public function update($request, $id)
    {
        try {
            return DB::transaction(function () use ($request, $id) {
                $inputs = $this->getInputs($this->model->getFillable(), $request);
                $relations = $this->getRelations($this->model->relation_methods, $request);

                $data = $this->model::find($id);
                foreach ($inputs as $inputKey => $inputValue) {
                    $data->$inputKey = $inputValue;
                }
                $data->save();

                foreach ($relations as $relation) {
                    $relationKey = array_key_first($relation);
                    $relationData = $relation[$relationKey];

                    /**
                     * @var Model $relationModel
                     */
                    $relationModel = $this->model->{$relationKey}()->getModel();

                    if (count($relationData) == count($relationData, COUNT_RECURSIVE)) {
                        // hasOne type relation
                        $relationInputs = $this->getUpdateInputs($relationModel->getFillable(), $relationData, $relationModel->getKeyName());
                        $relationInputs = array_merge($relationInputs, [$this->model->getMigrateKey() => $data->getKey()]);

                        $tempData = $this->relationModel::find($relationInputs[$relationModel->getKeyName()]);
                        foreach ($relationInputs as $inputKey => $inputValue) {
                            $tempData->$inputKey = $inputValue;
                        }
                        $tempData->save();
                    } else {
                        // hasMany type relation
                        if ($request->has('withDestroyRelationModel') && $request->get('withDestroyRelationModel') === true) {
                            $deletedIds = $this->getDeletedIds($relationData, $relationModel->getKeyName(), $relationModel);
                            $relationModel::destroy($deletedIds);
                        }
                        
                        foreach ($relationData as $relationValue) {

                            $relationInputs = $this->getUpdateInputs($relationModel->getFillable(), $relationValue, $relationModel->getKeyName());
                            $relationInputs = array_merge($relationInputs, [$this->model->getMigrateKey() => $data->getKey()]);
                            
                            if (array_key_exists($relationModel->getKeyName(), $relationInputs) && $relationInputs[$relationModel->getKeyName()] != null) {
                                $tempData = $relationModel::find($relationInputs[$relationModel->getKeyName()]);
                                foreach ($relationInputs as $inputKey => $inputValue) {
                                    $tempData->$inputKey = $inputValue;
                                }
                                $tempData->save();
                            } else {
                                $relationModel->create($relationInputs);
                            }
                        }
                    }

                    $data->load($relationKey);
                }

                return $this->respondOk($data);
            });
        } catch (\Exception $e) {
            return $this->respondError($e);
        }
    }

    /**
     * Delete ressource
     * 
     * @param int|array $id
     */
    public function delete($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $data = $this->model::find($id);
                $this->model::destroy($id);
                return $this->respondOk($data);
            });
        } catch (\Exception $e) {
            return $this->respondError($e);
        }
    }

    /**
     * @param Request $request
     * @param int $id
     */
    public function show($request, $id)
    {
        $data = $this->parse_filters($request)->where($this->model->getKeyName(), $id)->first();
        return $this->respondOk($data);
    }

    /**
     * 
     * @param Request $request
     * @param array $rules
     * @return true|array
     */
    public function check($request, $rules = [], $id = null)
    {
        $requestInput = is_array($request) ? $request : $request->all();
        $requestInput = is_null($id) ? $requestInput : array_merge($requestInput, [$this->model->getKeyName() => $id]);

        $rules = $this->getRules($rules, $id);

        if (empty($rules)) {
            $rules = $this->repository->rules();
        }

        $validator = Validator::make($requestInput, $rules);
        if ($validator->fails()) {
            return $this->respondBadRequest($validator->errors()->messages());
        }
        return true;
    }

    public function getRules($rules, $id)
    {
        $response = [];
        foreach ($rules as $key => $rule) {
            $rule = is_array($rule) ? $rule : explode('|', $rule);
            if (in_array(IModel::IGNORE_RULE, $rule)) {
                $rule = array_merge(
                    $rule,
                    [Rule::unique($this->model->getTable(), $key)->ignore($id)]
                );

                unset($rule[array_search(IModel::IGNORE_RULE, $rule)]);
            }
            $response[$key] = $rule;
        }
        return $response;
    }
}
