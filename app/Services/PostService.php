<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\DB;

class PostService
{
    /**
     * @param $id
     * @return Post
     */
    public function retrieve($id): Post
    {
        return Post::findOrFail($id);
    }

    /**
     * @return array
     */
    public function list(): array
    {
        return Post::all();
    }

    /**
     * Create and save a Post based on $data
     * @param array $data
     * @return Post
     */
    public function create(array $data): Post
    {
        // Create the new post model
        return Post::create($data);
    }

    /**
     * Retrieve, modify and save a Post based on $data
     * The number of affected rows will be retrieved
     * @param array $data
     * @return int
     */
    public function modify(array $data): int
    {
        // as the ID was validated in the controller, let's avoid retrieve it again
        return DB::table('posts')
            ->where('id', $data['id'])
            ->update(
                [
                    'title'             => $data['title'],
                    'description'       => $data['description'],
                    'long_description'  => $data['long_description'],
                    'image_url'         => $data['image_url'],
                    'category_id'       => $data['category_id']
                ]
            );
    }

    /**
     * Retrieve and delete a Post
     * @param $id
     */
    public function delete($id): void
    {
        $post = Post::findOrFail($id);
        $post->delete();
    }
}
