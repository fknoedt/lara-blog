## Simple Blog Application 

This is a simple Blog implementation built with Laravel and MySQL in the Back End and Vue.js and Bootstrap 4 in the Front End using the following features, methodologies and standards:
 
 * [NestedSet](https://github.com/lazychaser/laravel-nestedset) Package (to implement a nested category structure)
 * MVC + Services
 * Eloquent Models & Resources
 * Authentication Middleware
 * Database Migration and Seeders
 * Blade templates
 * PHP7 / PSRs
 * Vue.js
 * Bootstrap 4
 * NPM
 * Webpack

### Installation

1) Clone this repository and run `composer install`
2) Copy or rename `/.env.example` to `/.env`
3) Create your Database and set it's configuration on the .env file
4) Run `php artisan migrate` to create the database structure and generate - through a seeder - the required data
5) Run `php artisan db:seed --class=DummyPostsAndCategories` to populate the database with dummy data (for demo purposes)
6) Run `npm install`
7) Run `npm run production`
8) Setup a web server and visit `/blog` or `/admin` to those respective interfaces   


### Testing

TDD was used not only for it's advantages but also to allow that the Endpoints could be tested with no Client Tool (for evaluation purposes). 

[PHPUnit](https://phpunit.de/) was used and Feature Tests were implemented to cover each of the API's Endpoints.

The database was mocked by creating, on the fly, a testing instance of it and running migrations and seeders on it.

The `.env.testing` (see below) directive `DB_PERSIST_TEST_DATA` (0 or 1) allows the data generated during the tests to be persisted after the tests have finished.

To run PHPUnit Tests, copy the `/.env` file to `/.env.testing` with the following changes:
   * `APP_ENV` should be `testing`
   * `DB_DATABASE` should end with `-test`
   * if you want the Test's data to be persisted, add a directive `DB_PERSIST_TEST_DATA=1`  

### Custom Code

These are the paths where there are scripts I wrote or customized:

 * `/app/Http/Controller/Api` (CategoryController.php, PostController.php)
 * `/app/Models`
 * `/app/Services`
 * `/database/migrations` (%category%, %post%)
 * `/database/seeds` (InitialData.php, DummyPostsAndCategories.php)
 * `/routes` (api.php, web.php)
 * `/tests`
 * `/tests/Feature`
 * `/resource/js` (app.js)
 * `/resource/js/admin-components`
 * `/resource/js/components`
 * `/resource/js/views`
 * `/resource/views`

_* if not specified, every file in the directory was customized_

### TODO

 * implement Categories select =(
 * fix Admin panel style
 * Improve CKEditor style
 * Improve error handling
 * Use SQLite In Memory Database to improve the Test Cases' performance
