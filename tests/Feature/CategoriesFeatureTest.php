<?php

namespace Tests\Feature;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\BaseTestCase;

/**
 * Class CategoriesFeatureTest
 * Test the /api/categories endpoint for success and error operations
 * @package Tests\Feature
 */
class CategoriesFeatureTest extends BaseTestCase
{
    /**
     * Used to validate the REST API responses
     */
    const VALID_JSON_STRUCTURE = [
        'name',
        'slug',
        'user_id',
        'parent_id',
        '_lft',
        '_rgt',
        'updated_at',
        'created_at'
    ];

    /**
     * dataProvider: consistent Categories
     * @return array
     */
    public function validCategories()
    {
        return [
            [
                [
                    'slug'  => 'first-category',
                    'name'  => 'First Category'
                ]
            ],
            [
                [
                    'slug'      => 'children-category',
                    'name'      => 'Children Category',
                    'parent_id' => self::DEFAULT_CATEGORY_ID
                ]
            ],
            [
                [
                    'id'        => 3,
                    'slug'      => 'very-long-category-slug-very-long-category-slug-very-long-category-slug-very-long-category-slug-very-long-category-slug-very-long-category-slug-very-long-category-slug-very-long-category-slug',
                    'name'      => 'Third Category',
                ]
            ],

        ];
    }

    /**
     * dataProvider: inconsistent Categories
     * @return array
     */
    public function invalidCategories()
    {
        return [
            [
                [
                    'id'    => 'Invalid ID',
                    'slug'  => 'invalid-category',
                    'name'  => 'Invalid Category'
                ]
            ],
            [
                [
                    'slug'      => 'invalid-category',
                    // no 'name'
                    'parent_id' => 1
                ]
            ],
            [
                [
                    'id'    => 'Invalid ID',
                    // no 'slug'
                    'name'  => 'Invalid Category'
                ]
            ],
            [
                [
                    'id'    => 'Invalid ID',
                    'slug'  => 'invalid-category',
                    'name'  => 'Invalid Category',
                    'parent_id' => 99 // invalid parent_id
                ]
            ],

        ];
    }

    /**
     * @test
     * GET /api/categories - OK
     * @return void
     */
    public function testGetCategoriesSuccessfully()
    {
        $response = $this->get('/api/categories/' . self::MOCKED_CATEGORY_ID, $this->defaultHeader);

        $response->assertStatus(200);

        $response->assertJsonStructure(self::VALID_JSON_STRUCTURE);
    }

    /**
     * @test
     * GET /api/categories - Error
     * @return void
     */
    public function testGetCategoriesError()
    {
        $this->expectException(ModelNotFoundException::class);

        $response = $this->get('/api/categories/100000', $this->defaultHeader);

        $response->assertStatus(404);
    }

    /**
     * @test
     * POST /api/categories - OK
     * @dataProvider validCategories
     * @param array $data
     * @return void
     */
    public function testPostCategoriesSuccessfully($data): void
    {
        $response = $this->post('/api/categories', $data, $this->defaultHeader);

        $response->assertStatus(200);

        $response->assertJsonStructure(
            self::VALID_JSON_STRUCTURE
        );
    }

    /**
     * @test
     * POST /api/categories - Error
     * @dataProvider invalidCategories
     * @return void
     */
    public function testPostCategoriesError($data)
    {
        $this->expectException(HttpException::class);

        $response = $this->post('/api/categories', $data, $this->defaultHeader);

        $response->assertStatus(422);
    }

    /**
     * @test
     * PUT /api/categories/{id} - OK
     * @return void
     */
    public function testPutCategoriesSuccessfully(): void
    {
        $data = [
            'slug'  => 'modified-category',
            'name'  => 'Modified Category'
        ];

        $response = $this->put('/api/categories/' . BaseTestCase::MOCKED_CATEGORY_ID, $data, $this->defaultHeader);

        $response->assertStatus(200);

        $response->assertJsonStructure(
            ['response']
        );
    }

    /**
     * @test
     * PUT /api/categories - Error
     * @return void
     */
    public function testPutCategoriesError()
    {
        $this->expectException(HttpException::class);

        $response = $this->put('/api/categories/1000000', [], $this->defaultHeader);

        $response->assertStatus(422);
    }

    /**
     * @test
     * DELETE /api/categories - OK
     * @return void
     */
    public function testDeleteCategoriesSuccessfully()
    {
        $response = $this->delete('/api/categories/' . self::MOCKED_CATEGORY_ID, [], $this->defaultHeader);

        $response->assertStatus(200);

        $response->assertJsonStructure(['response']);
    }

    /**
     * @test
     * DELETE /api/categories - Error
     * @return void
     */
    public function testDeleteCategoriesError()
    {
        $this->expectException(ModelNotFoundException::class);

        $response = $this->delete('/api/categories/100000', [], $this->defaultHeader);

        $response->assertStatus(404);
    }
}
