## Introduction

[![Latest Stable Version](https://poser.pugx.org/turahe/laravel-likeable/v)](//packagist.org/packages/turahe/laravel-likeable)
[![Total Downloads](https://poser.pugx.org/turahe/laravel-likeable/downloads)](//packagist.org/packages/turahe/laravel-likeable)
[![License](https://poser.pugx.org/turahe/laravel-likeable/license)](//packagist.org/packages/turahe/laravel-likeable)
[![Tests](https://github.com/turahe/laravel-likeable/actions/workflows/tests.yml/badge.svg)](https://github.com/turahe/laravel-likeable/actions/workflows/tests.yml)
[![Code Coverage](https://codecov.io/gh/turahe/laravel-likeable/branch/main/graph/badge.svg)](https://codecov.io/gh/turahe/laravel-likeable)

Laravel Likeable simplifies management of Eloquent model's likes & dislikes. Make any model `likeable` & `dislikeable` in a minute!

## Contents

- [Features](#features)
- [Installation](#installation)
- [Usage](#usage)
    - [Prepare likeable model](#prepare-likeable-model)
    - [Available methods](#available-methods)
    - [Scopes](#scopes)
    - [Events](#events)
    - [Console commands](#console-commands)
- [Extending](#extending)
- [Change log](#change-log)
- [Contributing](#contributing)
- [Testing](#testing)
- [Continuous Integration](#continuous-integration)
- [Security](#security)
- [Contributors](#contributors)
- [Alternatives](#alternatives)
- [License](#license)
- [About Turahe](#about-turahe)

## Features

- ✅ **Designed to work with Laravel Eloquent models**
- ✅ **Using contracts to keep high customization capabilities**
- ✅ **Using traits to get functionality out of the box**
- ✅ **Most part of the logic is handled by the `LikeableService`**
- ✅ **Has Artisan command `likeable:recount {model?} {type?}` to re-fetch likes counters**
- ✅ **Likeable model can have Likes and Dislikes**
- ✅ **Likes and Dislikes for one model are mutually exclusive**
- ✅ **Get Likeable models ordered by likes count**
- ✅ **Events for `like`, `unlike`, `dislike`, `undislike` methods**
- ✅ **Comprehensive test coverage (79 tests, 0 failures)**
- ✅ **Following PHP Standard Recommendations:**
  - [PSR-1 (Basic Coding Standard)](http://www.php-fig.org/psr/psr-1/)
  - [PSR-2 (Coding Style Guide)](http://www.php-fig.org/psr/psr-2/)
  - [PSR-4 (Autoloading Standard)](http://www.php-fig.org/psr/psr-4/)
- ✅ **Fully tested with PHPUnit**
- ✅ **Continuous Integration with GitHub Actions**
- ✅ **Code style checks with PHP CS Fixer**
- ✅ **Static analysis with Larastan**


## Installation

First, pull in the package through Composer.

```sh
$ composer require turahe/laravel-likeable
```

**If you are using Laravel 5.5+ you can skip the register package part.** 

#### Register package on Laravel 5.4 and lower

Include the service provider within `app/config/app.php`.

```php
'providers' => [
    Turahe\Likeable\LikeableServiceProvider::class,
],
```

#### Perform Database Migration

At last, you need to publish and run database migrations.

```sh
$ php artisan vendor:publish --provider="Turahe\Likeable\LikeableServiceProvider" --tag=migrations
$ php artisan migrate
```

## Usage

### Prepare likeable model

Use `Likeable` contract in model which will get likes behavior and implement it or just use `Likeable` trait. 

```php
use Turahe\Likeable\Contracts\Likeable as LikeableContract;
use Turahe\Likeable\Traits\Likeable;
use Illuminate\Database\Eloquent\Model;

class Article extends Model implements LikeableContract
{
    use Likeable;
}
```

### Available methods

#### Likes

##### Like model

```php
$article->like(); // current user
$article->like($user->id);
```

##### Remove like mark from model

```php
$article->unlike(); // current user
$article->unlike($user->id);
```

##### Toggle like mark of model

```php
$article->likeToggle(); // current user
$article->likeToggle($user->id);
```

##### Get model likes count

```php
$article->likesCount;
```

##### Get model likes counter

```php
$article->likesCounter;
```

##### Get likes relation

```php
$article->likes();
```

##### Get iterable `Illuminate\Database\Eloquent\Collection` of existing model likes

```php
$article->likes;
```

##### Boolean check if user liked model

```php
$article->liked; // current user
$article->liked(); // current user
$article->liked($user->id);
```

*Checks in eager loaded relations `likes` & `likesAndDislikes` first.*

##### Get collection of users who liked model

```php
$article->collectLikers();
```

##### Delete all likes for model

```php
$article->removeLikes();
```

#### Dislikes

##### Dislike model

```php
$article->dislike(); // current user
$article->dislike($user->id);
```

##### Remove dislike mark from model

```php
$article->undislike(); // current user
$article->undislike($user->id);
```

##### Toggle dislike mark of model

```php
$article->dislikeToggle(); // current user
$article->dislikeToggle($user->id);
```

##### Get model dislikes count

```php
$article->dislikesCount;
```

##### Get model dislikes counter

```php
$article->dislikesCounter;
```

##### Get dislikes relation

```php
$article->dislikes();
```

##### Get iterable `Illuminate\Database\Eloquent\Collection` of existing model dislikes

```php
$article->dislikes;
```

##### Boolean check if user disliked model

```php
$article->disliked; // current user
$article->disliked(); // current user
$article->disliked($user->id);
```

*Checks in eager loaded relations `dislikes` & `likesAndDislikes` first.*

##### Get collection of users who disliked model

```php
$article->collectDislikers();
```

##### Delete all dislikes for model

```php
$article->removeDislikes();
```

#### Likes and Dislikes

##### Get difference between likes and dislikes

```php
$article->likesDiffDislikesCount;
```

##### Get likes and dislikes relation

```php
$article->likesAndDislikes();
```

##### Get iterable `Illuminate\Database\Eloquent\Collection` of existing model likes and dislikes

```php
$article->likesAndDislikes;
```

### Scopes

##### Find all articles liked by user

```php
Article::whereLikedBy($user->id)
    ->with('likesCounter') // Allow eager load (optional)
    ->get();
```

##### Find all articles disliked by user

```php
Article::whereDislikedBy($user->id)
    ->with('dislikesCounter') // Allow eager load (optional)
    ->get();
```

##### Fetch Likeable models by likes count

```php
$sortedArticles = Article::orderByLikesCount()->get();
$sortedArticles = Article::orderByLikesCount('asc')->get();
```

*Uses `desc` as default order direction.*

##### Fetch Likeable models by dislikes count

```php
$sortedArticles = Article::orderByDislikesCount()->get();
$sortedArticles = Article::orderByDislikesCount('asc')->get();
```

*Uses `desc` as default order direction.*

### Events

On each like added `\Turahe\Likeable\Events\ModelWasLiked` event is fired.

On each like removed `\Turahe\Likeable\Events\ModelWasUnliked` event is fired.

On each dislike added `\Turahe\Likeable\Events\ModelWasDisliked` event is fired.

On each dislike removed `\Turahe\Likeable\Events\ModelWasUndisliked` event is fired.

### Console commands

##### Recount likes and dislikes of all model types

```sh
$ php artisan likeable:recount
```

##### Recount likes and dislikes of concrete model type (using morph map alias)

```sh
$ php artisan likeable:recount --model="article"
```

##### Recount likes and dislikes of concrete model type (using fully qualified class name)

```sh
$ php artisan likeable:recount --model="App\Models\Article"
```

##### Recount only likes of all model types

```sh
$ php artisan likeable:recount --type="like"
```

##### Recount only likes of concrete model type (using morph map alias)

```sh
$ php artisan likeable:recount --model="article" --type="like"
```

##### Recount only likes of concrete model type (using fully qualified class name)

```sh
$ php artisan likeable:recount --model="App\Models\Article" --type="like"
```

##### Recount only dislikes of all model types

```sh
$ php artisan likeable:recount --type="dislike"
```

##### Recount only dislikes of concrete model type (using morph map alias)

```sh
$ php artisan likeable:recount --model="article" --type="dislike"
```

##### Recount only dislikes of concrete model type (using fully qualified class name)

```sh
$ php artisan likeable:recount --model="App\Models\Article" --type="dislike"
```

## Extending

You can override core classes of package with your own implementations:

- `Models\Like`
- `Models\LikeCounter`
- `Services\LikeableService`

*Note: Don't forget that all custom models must implement original models interfaces.*

To make it you should use container [binding interfaces to implementations](https://laravel.com/docs/master/container#binding-interfaces-to-implementations) in your application service providers.

##### Use model class own implementation

```php
$this->app->bind(
    \Turahe\Likeable\Contracts\Like::class,
    \App\Models\CustomLike::class
);
```

##### Use service class own implementation

```php
$this->app->singleton(
    \Turahe\Likeable\Contracts\LikeableService::class,
    \App\Services\CustomService::class
);
```

After that your `CustomLike` and `CustomService` classes will be instantiable with helper method `app()`.

```php
$model = app(\Turahe\Likeable\Contracts\Like::class);
$service = app(\Turahe\Likeable\Contracts\LikeableService::class);
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Testing

This package includes comprehensive test coverage with 79 tests covering all functionality:

- **Common Use Tests** - Basic like/unlike functionality
- **Counter Tests** - Like/dislike counter management
- **Dislike Tests** - Dislike-specific functionality
- **Enum Tests** - LikeType enum functionality
- **Event Tests** - Event dispatching
- **Exception Tests** - Custom exception handling
- **Service Tests** - Service layer functionality
- **Toggle Tests** - Like/dislike toggle operations
- **Console Command Tests** - Artisan command functionality

### Running Tests

You can run the tests with:

```sh
$ vendor/bin/phpunit
```

Or use the composer script:

```sh
$ composer test
```

### Test Coverage

Run tests with coverage:

```sh
$ composer test:coverage
```

This will generate HTML coverage reports in the `coverage-html` directory.

## Continuous Integration

This package uses GitHub Actions for continuous integration. The CI pipeline includes:

### Test Matrix
- **PHP Versions**: 8.3, 8.4
- **Laravel Versions**: 10.x, 11.x, 12.x
- **Test Coverage**: Generated for PHP 8.4 + Laravel 12.x

### CI Jobs
1. **Tests**: Runs the full test suite across all PHP/Laravel combinations
2. **Static Analysis**: Code style checking with PHP CS Fixer and Larastan across all PHP/Laravel combinations
3. **Lint**: PHP syntax validation
4. **Coverage**: Code coverage reporting

### Local Development
You can run the same checks locally:

```sh
# Run all checks
$ composer check

# Run specific checks
$ composer test          # Run tests
$ composer test:coverage # Run tests with coverage
$ composer lint          # Check code style
$ composer analyse       # Run static analysis
```

### Code Coverage
Code coverage reports are generated and uploaded to [Codecov](https://codecov.io/gh/turahe/laravel-likeable).

## Security

If you discover any security related issues, please email wachid@outlook.com instead of using the issue tracker.

## Contributors

- [Nur Wachid](https://github.com/turahe) - *Initial work*

## Alternatives

- [spatie/laravel-likes](https://github.com/spatie/laravel-likes) - A simple package to add likes to Eloquent models
- [rtconner/laravel-likeable](https://github.com/rtconner/laravel-likeable) - Likeable trait for Laravel Eloquent models

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## About Turahe

Turahe is a software development company focused on creating high-quality Laravel packages and applications. Visit us at [turahe.id](https://turahe.id).
