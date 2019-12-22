<?php

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;
use \Tests\BaseTestCase;

class InitialData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // default user for testing (required for authentication and for the other records' FKs)
        DB::table('users')->insert(
            [
                [
                    'id'        => BaseTestCase::DEFAULT_USER_ID,
                    'name'      => 'Admin',
                    'email'     => 'admin@example.com',
                    'password'  => bcrypt('password'),
                    'api_token' => '14IjyOhUjG9MYyzp9HZdgu1cIKHuPIXG2S2pTZppJYjN5EXcCu0qpi6Rx7Oo',
                ],
                [
                    'id'        => BaseTestCase::MOCKED_USER_ID,
                    'name'      => 'Delete Me',
                    'email'     => 'no-reply@example.com',
                    'password'  => bcrypt('password'),
                    'api_token' => 'x4IjyOhUjG9MYyzp9HZdgu1cIKHuPIXG2S2pTZppJYjN5EXcCu0qpi6Rx7Ox',
                ],
            ]
        );

        // using the model so we can leverage NestedSet
        \App\Models\Category::create([
            'id' => BaseTestCase::DEFAULT_CATEGORY_ID,
            'slug' => 'default-category',
            'name' => 'Default Category',
            'user_id' => BaseTestCase::DEFAULT_USER_ID
        ]);

        \App\Models\Category::create([
            'id' => BaseTestCase::MOCKED_CATEGORY_ID,
            'slug' => 'mocked-category',
            'name' => 'Mocked Category',
            'parent_id' => BaseTestCase::DEFAULT_CATEGORY_ID,
            'user_id' => BaseTestCase::DEFAULT_USER_ID
        ]);

        // use the factory to create a Faker\Generator instance
        $faker = Faker\Factory::create();

        DB::table('posts')->insert([
            'id'                => BaseTestCase::MOCKED_POST_ID,
            'title'             => 'This is a Test Post',
            'description'       => 'This is a Post to allow Feature Testing a Post retrieval',
            'long_description'  => $faker->text(1000),
            'image_url'         => 'https://picsum.photos/640/480', // $faker->imageUrl(): lorempixel.com is failing too often
            'user_id'           => BaseTestCase::DEFAULT_USER_ID,
            'category_id'       => BaseTestCase::DEFAULT_CATEGORY_ID
        ]);
    }
}
