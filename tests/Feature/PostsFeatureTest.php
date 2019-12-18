<?php

namespace Tests\Feature;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\BaseTestCase;

/**
 * Class PostsFeatureTest
 * Test the /api/posts endpoint for success and error operations
 * @package Tests\Feature
 */
class PostsFeatureTest extends BaseTestCase
{
    /**
     * Used to validate the REST API responses
     */
    const VALID_JSON_STRUCTURE = [
        'id',
        'title',
        'description',
        'long_description',
        'image_url',
        'user_id',
        'category_id',
    ];

    /**
     * dataProvider: consistent posts
     * @return array
     */
    public function validPosts()
    {
        return [
            [
                [
                    'title'             => 'This is the First Post',
                    'description'       => 'This is a Post to allow Feature Testing a Post retrieval',
                    'long_description'  => 'The Long description goes here',
                    'image_url'         => 'http://lorempixel.com/640/480/',
                    'user_id'           => BaseTestCase::DEFAULT_USER_ID,
                    'category_id'       => BaseTestCase::DEFAULT_CATEGORY_ID
                ]
            ],
            [
                [
                    'id'                => 1000,
                    'title'             => 'This is a Post with a fixed ID',
                    'description'       => 'The Description goes here',
                    'long_description'  => 'The Long Description goes here',
                    'image_url'         => 'http://lorempixel.com/640/480/',
                    'user_id'           => BaseTestCase::DEFAULT_USER_ID,
                    'category_id'       => BaseTestCase::DEFAULT_CATEGORY_ID
                ]
            ]
        ];
    }

    /**
     * dataProvider: inconsistent Posts
     * @return array
     */
    public function invalidPosts()
    {
        return [
            [
                [
                    'title'             => 'This is a Post with no Description',
                    'long_description'  => 'The Long Description goes here',
                    'image_url'         => 'http://lorempixel.com/640/480/',
                    'user_id'           => BaseTestCase::DEFAULT_USER_ID,
                    'category_id'       => BaseTestCase::DEFAULT_CATEGORY_ID
                ]
            ],
            [
                [
                    'title'             => 'This is a Post with no Long Description',
                    'description'       => 'The Description goes here',
                    'image_url'         => 'http://lorempixel.com/640/480/',
                    'user_id'           => BaseTestCase::DEFAULT_USER_ID,
                    'category_id'       => BaseTestCase::DEFAULT_CATEGORY_ID
                ]
            ],
            [
                [
                    'title'             => 'This is a Post with no category_id',
                    'description'       => 'The Description goes here',
                    'long_description'  => 'The Long Description goes here',
                    'image_url'         => 'http://lorempixel.com/640/480/',
                    'user_id'           => BaseTestCase::DEFAULT_USER_ID
                ]
            ],
            [
                [
                    'title'             => 'This is a Post with no Image URL',
                    'description'       => 'The Description goes here',
                    'long_description'  => 'The Long Description goes here',
                    'user_id'           => BaseTestCase::DEFAULT_USER_ID,
                    'category_id'       => BaseTestCase::DEFAULT_CATEGORY_ID
                ]
            ],
            [
                [
                    'title'             => 'This is a Post with a wrong Image URL',
                    'description'       => 'The Description goes here',
                    'long_description'  => 'The Long Description goes here',
                    'image_url'         => 'invalid url',
                    'user_id'           => BaseTestCase::DEFAULT_USER_ID,
                    'category_id'       => BaseTestCase::DEFAULT_CATEGORY_ID
                ]
            ]
        ];
    }

    /**
     * @test
     * GET /api/posts - OK
     * @return void
     */
    public function testGetPostsSuccessfully()
    {
        $response = $this->get('/api/posts/' . self::MOCKED_POST_ID, $this->defaultHeader);

        $response->assertStatus(200);

        $response->assertJsonStructure(self::VALID_JSON_STRUCTURE);
    }

    /**
     * @test
     * GET /api/posts - Error
     * @return void
     */
    public function testGetPostsError()
    {
        $this->expectException(ModelNotFoundException::class);

        $response = $this->get('/api/posts/100000', $this->defaultHeader);

        $response->assertStatus(404);
    }

    /**
     * @test
     * POST /api/posts - OK
     * @dataProvider validPosts
     * @param array $data
     * @return void
     */
    public function testPostPostsSuccessfully($data): void
    {
        $response = $this->post('/api/posts', $data, $this->defaultHeader);

        $response->assertStatus(200);

        $response->assertJsonStructure(
            self::VALID_JSON_STRUCTURE
        );
    }

    /**
     * @test
     * POST /api/posts - Error
     * @dataProvider invalidPosts
     * @return void
     */
    public function testPostPostsError($data)
    {
        $this->expectException(HttpException::class);

        $response = $this->post('/api/posts', $data, $this->defaultHeader);

        $response->assertStatus(422);
    }

    /**
     * @test
     * PUT /api/posts/{id} - OK
     * @return void
     */
    public function testPutPostsSuccessfully(): void
    {
        $data = [
            'title'             => 'Modified Title',
            'description'       => 'Modified Description',
            'long_description'  => 'Modified Long Description',
            'image_url'         => 'http://lorempixel.com/64/48/',
            'category_id'       => BaseTestCase::MOCKED_CATEGORY_ID,
        ];

        $response = $this->put('/api/posts/' . BaseTestCase::MOCKED_POST_ID, $data, $this->defaultHeader);

        $response->assertStatus(200);

        $response->assertJsonStructure(
            ['response']
        );
    }

    /**
     * @test
     * PUT /api/posts - Error
     * @return void
     */
    public function testPutPostsError()
    {
        $this->expectException(HttpException::class);

        $response = $this->put('/api/posts/1000000', [], $this->defaultHeader);

        $response->assertStatus(422);
    }

    /**
     * @test
     * DELETE /api/posts - OK
     * @return void
     */
    public function testDeletePostsSuccessfully()
    {
        $response = $this->delete('/api/posts/' . self::MOCKED_POST_ID, [], $this->defaultHeader);

        $response->assertStatus(200);

        $response->assertJsonStructure(['response']);
    }

    /**
     * @test
     * DELETE /api/posts - Error
     * @return void
     */
    public function testDeletePostsError()
    {
        $this->expectException(ModelNotFoundException::class);

        $response = $this->delete('/api/posts/100000', [], $this->defaultHeader);

        $response->assertStatus(404);
    }
}
