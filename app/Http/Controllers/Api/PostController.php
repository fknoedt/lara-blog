<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
     * List all posts
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        // retrieve all posts and return as JSON
        $posts = $this->service->list();
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $input = $request->all();

        // let's leverage Laravel's database validation
        $validator = Validator::make($input, [
            'id'                => 'numeric|unique:posts,id',
            'title'             => 'required',
            'description'       => 'required',
            'long_description'  => 'required',
            'image_url'         => 'required|url',
            'category_id'       => 'required|numeric|exists:categories,id'
        ]);

        $input['user_id'] = $request->user()->id;

        // failure: 422 (unprocessable entry) response
        if ($validator->fails()) {
            abort(422, 'Invalid input: ' . $validator->errors());
        }

        $post = $this->service->create($input);

        return response()->json($post->toArray());
    }

    /**
     * Update a Post in the database
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
            'id'                => 'numeric|required|exists:posts,id',
            'title'             => 'required',
            'description'       => 'required',
            'long_description'  => 'required',
            'image_url'         => 'url',
            'category_id'       => 'required|numeric|exists:categories,id'
        ]);

        $input['user_id'] = $request->user()->id;

        // failure: 422 (unprocessable entry) response
        if ($validator->fails()) {
            abort(422, 'Invalid input: ' . $validator->errors());
        }

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
