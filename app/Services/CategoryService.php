<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    /**
     * @param $id
     * @return Category
     */
    public function retrieve($id): Category
    {
        return Category::findOrFail($id);
    }

    /**
     * @return array
     */
    public function list()
    {
        return Category::all()->sortBy('parent_id');
    }

    /**
     * Create and save a Category based on $data
     * @param array $data
     * @return Category
     */
    public function create(array $data): Category
    {
        // Create the new category model (the parent_id will work with NestedSet)
        return Category::create($data);
    }

    /**
     * Retrieve, modify and save a Category based on $data
     * The number of affected rows will be retrieved
     * @param array $data
     * @return int
     */
    public function modify(array $data): int
    {
        // as the ID was validated in the controller, let's avoid retrieve it again
        return DB::table('categories')
            ->where('id', $data['id'])
            ->update(
                [
                    'slug' => $data['slug'],
                    'name' => $data['name'],
                ]
            );
    }

    /**
     * Retrieve and delete a Category
     * @param $id
     */
    public function delete($id): void
    {
        $category = Category::findOrFail($id);
        $category->delete();
    }
}
