# Yard Data Objects

Powerful data objects for WordPress.

## Requirements

- [Sage](https://github.com/roots/sage) >= 10.0
- [Acorn](https://github.com/roots/acorn) >= 3.0

## Installation

You can install this package with Composer:

```bash
composer require yard/data
```

You can publish the config file with:

```shell
$ wp acorn vendor:publish --provider="Yard\Data\Providers\DataServiceProvider"
```

## Usage

### PostData

#### Creating PostData

PostData can be created from the global `\WP_Post` object:

```php
global $post;
$postData = \Yard\Data\PostData::from($post);
```

From an array:

```php
$postData = \Yard\Data\PostData::from(
    [
        'id' => 42,
        'author' => 1,
        'title' => 'Hello, World!',
        'content' => 'This is a test post.',
        'excerpt' => 'This is a test post.',
        'status' => 'publish',
        'date' => '2021-01-01 00:00:00',
        'modified' => '2021-01-01 00:00:00',
        'postType' => 'post',
        'slug'=> 'hello-world',
    ]
);
```

Or from an Eloquent Model using Corcel:

```php
$model = \Corcel\Model\Post::find(get_the_ID());
$postData = \Yard\Data\PostData::from($model);
```

### Custom PostData

Creating a VacancyData object by extending PostData:

```php
namespace App\Data;

use Yard\Data\PostData;

class VacancyData extends PostData
{
}
```

Enables you to create VacancyData object in the same way as PostData:

```php
global $post;
$postData = \App\Data\VacancyData::from($post);
```

#### Configuring the returning Instance

Every time you call `Yard\Data\PostData::from($post)` you receive an instance of `Yard\Data\PostData`.

If you choose to create a new data class for your custom post type, you can have this class be returned for all instances of that post type.

Just use the config file and map all custom posts and it's classes:

```php
'post_types' => [
  'vacancy' => App\Data\VacancyData::class,
  'employee' => App\Data\EmployeeData::class,
```

Now every time you call `Yard\Data\PostData::from($post)` on a custom post type the mapped instance will be returned.

### Meta Fields

Adding a meta field with a meta_key of `vacancy_email` to your VacancyData looks like this:

```php
namespace App\Data;

use Yard\Data\Attributes\Meta;
use Yard\Data\PostData;

class VacancyData extends PostData
{
    #[Meta(metaKey: 'vacancy_email')]
    public string $email;
}
```

This approach is functionally equivalent to using:

```php
#[Meta]
public string $vacancyEmail;
```

If all of your meta fields are prefixed with the same prefix you can use the MetaPrefix attribute:

```php
namespace App\Data;

use Yard\Data\Attributes\MetaPrefix;
use Yard\Data\Attributes\Meta;
use Yard\Data\PostData;

#[MetaPrefix(prefix: 'vacancy')]
class VacancyData extends PostData
{
    #[Meta]
    public string $email;
}
```

### Taxonomy Terms

#### Adding terms to your Data Object

For every taxonomy that has been registered with your custom post type you can add a Collection of TermData like this:

```php
namespace App\Data;

use Illuminate\Support\Collection;
use Yard\Data\Attributes\Terms;
use Yard\Data\PostData;

class VacancyData extends PostData
{
    #[Terms(taxonomy: 'vacancy_location')]
    /** @var Collection<int, TermData> */
    public Collection $locations;
}
```

This approach is functionally equivalent to using:

```php
#[Terms]
/** @var Collection<int, TermData> */
public Collection $vacancyLocation;
```

or:

```php
#[Terms]
/** @var Collection<int, TermData> */
public Collection $vacancyLocations;
```

If all of your taxonomies are prefixed with the same prefix you can use the TaxonomyPrefix attribute:

```php
namespace App\Data;

use Illuminate\Support\Collection;
use Yard\Data\Attributes\TaxonomyPrefix;
use Yard\Data\Attributes\Terms;
use Yard\Data\PostData;

#[MetaPrefix(prefix: 'vacancy')]
#[TaxonomyPrefix(prefix: 'vacancy')]
class VacancyData extends PostData
{
    #[Terms]
    /** @var Collection<int, TermData> */
    public Collection $locations;
}
```

#### Reading Terms from your Data Object

Because Terms are a [Collection](https://laravel.com/docs/collections) you can use any of the available collection methods to read the terms from your data object. Here are some common examples:

```php
$vacancyData->locations->contains('slug',  'amsterdam'); // true
$postData->locations->firstWhere('slug', 'utrecht')->name; // Utrecht
$vacancyData->locations->implode('name', ', '), // Almere, Amsterdam, Utrecht
```

### UserData

Create UserData from current user:

```php
$userData = UserData::from(wp_get_current_user());
```
