<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;


class CategoryController extends Controller
{
    protected $service;

    /**
     * CategoryController constructor.
     */
    public function __construct()
    {
        // instead of instantiating it in every method, let's centralize (no DI needed here)
        $this->service = new CategoryService();
    }

    /**
     * List all Categories
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        // retrieve all categories and return as JSON
        $categories = $this->service->list();
        return response()->json($categories->toArray(), 200);
    }

    /**
     * Return a Category for the given ID
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $category = $this->service->retrieve($id);
        return response()->json($category->toArray(), 200);
    }

    /**
     * Save a Category to the database
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $input = $request->all();

        // let's leverage Laravel's database validation
        $validator = Validator::make($input, [
            'id'        => 'numeric|unique:categories,id',
            'name'      => 'required|unique:categories,name',
            'slug'      => 'required|unique:categories,slug',
            'parent_id' => 'numeric|exists:categories,id'
        ]);

        $input['user_id'] = $request->user()->id;

        // failure: 422 (unprocessable entry) response
        if ($validator->fails()) {
            abort(422, 'Invalid input: ' . $validator->errors());
        }

        $category = $this->service->create($input);

        return response()->json($category->toArray());
    }

    /**
     * Update a Category in the database
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     * @return string
     */
    public function update(Request $request, $id): JsonResponse
    {
        $input = $request->all();

        $input['id'] = $id;

        // let's leverage Laravel's database validation
        $validator = Validator::make($input, [
            'id'        => 'numeric|required|exists:categories',
            'name'      => 'required|unique:categories,name',
            'slug'      => 'required|unique:categories,slug'
        ]);

        $input['user_id'] = $request->user()->id;

        // failure: 422 (unprocessable entry) response
        if ($validator->fails()) {
            abort(422, 'Invalid input: ' . $validator->errors());
        }

        $this->service->modify($input);

        return response()->json(['response' => 'Category Updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $this->service->delete($id);

        return response()->json(['response' => 'Category Deleted']);
    }
}
