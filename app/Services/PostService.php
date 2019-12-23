<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\DB;

class PostService
{
    /**
     * used to set created/updated_at
     */
    const DATE_INPUT_FORMAT = 'Y-m-d H:i:s';

    /**
     * @param $id
     * @return Post
     */
    public function retrieve($id): Post
    {
        return Post::findOrFail($id);
    }

    /**
     * Retrieve all Posts [filtered by category_id]
     * @param null $categoryId
     * @return Post[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function list($categoryId=null)
    {
        // TODO: allow filter by category is null (e.g. if we want parents only)
        $posts = Post::with(['user']);

        // if filtering by Posts from a specific Category, let's include Posts from a sub-Category of that Category
        if ($categoryId) {
            $posts->where(['category_id' => $categoryId])
                ->orWhereHas('category', function($q) use ($categoryId) {
                    $q->where(['parent_id' => $categoryId]);
                });
        }

        return $posts->get();
    }

    /**
     * Create and save a Post based on $data
     * @param array $data
     * @return Post
     */
    public function create(array $data): Post
    {
        // ensure the column will be set
        $data['created_at'] = date(self::DATE_INPUT_FORMAT);
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
                    'category_id'       => $data['category_id'],
                    'updated_at'        => date(self::DATE_INPUT_FORMAT)
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
