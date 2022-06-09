<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Repository\Repository;
use App\Http\Controllers\Controller;
use App\Models\PermissionRole;
use Illuminate\Http\Request;

class PermissionRoleController extends Controller
{
    /**
     * @var PermissionRole
     */
    private $model;

    /**
     * @var Repository
     */
    private $repository;

    public function __construct()
    {
        $this->model = new PermissionRole();
        $this->repository = new Repository(PermissionRole::class);
    }

    /**
     * List resource
     * 
     * @param Request $request
     */
    public function index(Request $request, $id = null)
    {
        if ($id !== null) {
            return $this->show($request, $id);
        }

        $query = $this->repository->parse_filters($request);

        // return response
        if ($request->has('page') && $request->has('per_page')) {
            return $this->respondOk($query->paginate($request->get('per_page')));
        }
        return $this->respondOk($query->get());
    }

    /**
     * List single resource
     * 
     * @param Request $request
     * @param int $id
     */
    public function show(Request $request, $id)
    {
        return $this->repository->show($request, $id);
    }

    /**
     * Store resource
     * 
     * @param Request $request
     */
    public function store(Request $request)
    {
        $validator = $this->repository->check($request , $this->repository->getModel()->rules());
        if (true !== $validator) {
            return $validator;
        };

        return $this->repository->store($request);
    }

    /**
     * Update resource
     *  
     * @param Request $request
     * @param int $id
     */
    public function update(Request $request, $id)
    {
        $validator = $this->repository->check($request , $this->repository->update_rules(), $id);
        if (true !== $validator) {
            return $validator;
        };

        return $this->repository->update($request, $id);
    }

    /**
     * Delete resource
     * 
     * @param int $id
     */
    public function delete($id)
    {
        return $this->repository->delete($id);
    }
}
