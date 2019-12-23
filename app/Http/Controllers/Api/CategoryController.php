<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
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
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // retrieve all categories and return as JSON
        $categories = $this->service->list();
        $categories = $categories->toArray();
        // order indexes so it's properly handled in the front-end
        ksort($categories);
        return response()->json($categories, 200);
    }

    /**
     * Return a Category for the given ID
     *
     * @param  int  $id
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request, $id): JsonResponse
    {
        // allow request to the first category
        if ($id == 0 && $request->get('first')) {
            $category = Category::first();
            $id = $category->id;
        }

        $category = $this->service->retrieve($id);
        return response()->json($category->toArray(), 200);
    }

    /**
     * Save a Category to the database
     * @param  \Illuminate\Http\Request  $request
     * @throws \Illuminate\Validation\ValidationException
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $input = $request->all();

        // let's leverage Laravel's validation
        $this->validate($request, [
            'id'        => 'numeric|unique:categories,id',
            'name'      => 'required|unique:categories,name',
            'slug'      => 'required|unique:categories,slug',
            'parent_id' => 'numeric|exists:categories,id'
        ]);

        $input['user_id'] = $request->user()->id;

        $category = $this->service->create($input);

        return response()->json($category->toArray());
    }

    /**
     * Update a Category in the database
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @throws \Illuminate\Validation\ValidationException
     * @return \Illuminate\Http\JsonResponse
     * @return string
     */
    public function update(Request $request, $id): JsonResponse
    {
        $input = $request->all();

        $input['id'] = $id;

        // let's leverage Laravel's validation
        $this->validate($request, [
            'name'      => 'required|unique:categories,name',
            'slug'      => 'required|unique:categories,slug,' . $id
        ]);

        $input['user_id'] = $request->user()->id;

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
