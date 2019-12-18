<?php

namespace Tests;

use App\Services\CategoryService;
use App\Services\PostService;

/**
 * Class BaseTestCase
 * This class is responsible for the common test configs, routines and data providers
 * @package Tests
 */
class BaseTestCase extends TestCase
{
    /**
     * setup in-memory or physical database
     */
    use DatabaseSetup;

    /**
     * Seeded Main User
     */
    const DEFAULT_USER_ID = 1;

    /**
     * User to be tested (and deleted)
     */
    const MOCKED_USER_ID = 9999;

    /**
     * Seeded Main Category (used as a NestedSet Parent)
     */
    const DEFAULT_CATEGORY_ID = 1;

    /**
     * Category to be tested
     */
    const MOCKED_CATEGORY_ID = 9999;

    /**
     * Post to be tested
     */
    const MOCKED_POST_ID = 9999;

    /**
     * Headers to be sent on the API Tests
     * @var array
     */
    protected $defaultHeader = [
        'Authorization' => 'Bearer 14IjyOhUjG9MYyzp9HZdgu1cIKHuPIXG2S2pTZppJYjN5EXcCu0qpi6Rx7Oo',
        'Accept'        => 'application/json',
        'Cache-Control' => 'no-cache'
    ];

    /**
     * @var App\Services\CategoryService
     */
    protected $categoryService;

    /**
     * @var App\Services\PostService
     */
    protected $postService;

    /**
     * BaseTestCase constructor.
     * @param null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        // we don't need DI for these services
        $this->postService = new PostService();
        $this->categoryService = new CategoryService();

        parent::__construct($name, $data, $dataName);
    }

    /**
     * This method runs before each test
     */
    protected function setUp(): void
    {
        parent::setUp();

        // let's make sure migration is up to date (once) and a transaction was started (before each test)
        $this->setupDatabase();

        // This disables the exception handling to display the stacktrace on the console the same way as it shown on the browser
        $this->withoutExceptionHandling();
    }

    /**
     * runs after each test
     */
    protected function tearDown(): void
    {
        // ensure transaction is rolled back
        $this->tearDownDatabase();

        parent::tearDown();
    }
}
