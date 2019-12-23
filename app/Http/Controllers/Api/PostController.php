<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;


class PostController extends Controller
{
    protected $service;

    /**
     * PostController constructor.
     */
    public function __construct()
    {
        // instead of instantiating it in every method, let's centralize (no DI needed here)
        $this->service = new PostService();
    }

    /**
     * List all posts [filtered by query string c=categoryId]
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $categoryId = $request->get('c') ?? null;

        if (! $categoryId && $request->get('first')) {
            $category = Category::first();
            if ($category) {
                $categoryId = $category->id;
            }
        }

        // retrieve all posts and return as JSON
        $posts = $this->service->list($categoryId);
        return response()->json($posts->toArray(), 200);
    }

    /**
     * Return a Post for the given ID
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $post = $this->service->retrieve($id);
        return response()->json($post->toArray(), 200);
    }

    /**
     * Save a Post to the database
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $input = $request->all();

        $this->validate($request, [
            'id'                => 'numeric|unique:posts,id',
            'title'             => 'required',
            'description'       => 'required',
            'long_description'  => 'required',
            // 'image_url'         => 'required|url',
            'category_id'       => 'required|numeric|exists:categories,id'
        ]);

        // logged user's ID
        $input['user_id'] = $request->user()->id;
        // hard-coded image to avoid handling file upload for now
        $input['image_url'] = 'https://picsum.photos/640/480?' . rand(1,10000);

        $post = $this->service->create($input);

        return response()->json($post->toArray());
    }

    /**
     * Update a Post in the database
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @return string
     */
    public function update(Request $request, $id): JsonResponse
    {
        $input = $request->all();

        $input['id'] = $id;

        $this->validate($request, [
            'id'                => 'numeric|unique:posts,id',
            'title'             => 'required',
            'description'       => 'required',
            'long_description'  => 'required',
            // 'image_url'         => 'required|url',
            'category_id'       => 'required|numeric|exists:categories,id'
        ]);

        $input['user_id'] = $request->user()->id;

        // hard-coded image to avoid handling file upload for now
        $input['image_url'] = 'https://picsum.photos/640/480?' . rand(1,10000);

        $this->service->modify($input);

        return response()->json(['response' => 'Post Updated']);
    }

    /**
     * Remove the specified Post from the database
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $this->service->delete($id);

        return response()->json(['response' => 'Post Deleted']);
    }
}
