<?php

use \App\Models\Category;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\BaseTestCase;

class DummyPostsAndCategories extends Seeder
{
    /**
     * Users to create
     */
    const TOTAL_USERS = 5;

    /**
     * Categories to create
     */
    const TOTAL_CATEGORIES = 10;

    /**
     * Posts to create
     */
    const TOTAL_POSTS = 20;

    /**
     * List of created records (to future reference)
     * @var array
     */
    protected $created = [];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // ensure ATOMicity
        DB::beginTransaction();

        // use the factory to create a Faker\Generator instance
        $faker = Faker\Factory::create();

        for ($i=0; $i < self::TOTAL_USERS; $i++) {


            // default user for testing (required for authentication and for the other records' FKs)
            $userId = DB::table('users')->insertGetId([
                'name'      => $faker->name(),
                'email'     => $faker->email(),
                'password' => Hash::make($faker->password()),
                'api_token' => Str::random(60),
            ]);

            // save every created ID
            $this->created['user'][$userId] = true;

        }

        for ($i=0; $i < self::TOTAL_CATEGORIES; $i++) {

            $parentId = null;

            // there's already at least one parent (able) id AND 75% of chance: make it a child category
            if (! empty($this->created['parentCategory']) && rand(0,2)) {
                $parentId = $this->getRandomElement($this->created['parentCategory']);
            }

            $category = $faker->word();

            $data = Category::create([
                'slug' => $category,
                'name' => ucwords($category),
                'parent_id' => $parentId,
                'user_id' => $this->getRandomElement(array_keys($this->created['user']))
            ]);

            $this->created['category'][$data->id] = true;

            // 50% of the categories who are not children can be parents
            if (! $parentId && rand(0,1)) {
                $this->created['parentCategory'][] = $data->id;
            }

        }

        for ($i=0; $i < self::TOTAL_POSTS; $i++) {

            DB::table('posts')->insert([
                'title'             => $faker->text(rand(15, 45)),
                'description'       => $faker->text(rand(30, 100)),
                'long_description'  => $faker->text(rand(500, 1500)),
                'image_url'         => 'https://picsum.photos/640/480?' . rand(1,10000), // $faker->imageUrl(): lorempixel.com is failing too often and this image will be randomized
                'user_id'           => $this->getRandomElement(array_keys($this->created['user'])),
                'category_id'       => $this->getRandomElement(array_keys($this->created['category']))
            ]);

        }

        DB::commit();
    }

    /**
     * @param array $a
     * @return mixed
     */
    public function getRandomElement(array $a)
    {
        $randomIndex = array_rand($a);
        return $a[$randomIndex];
    }
}
