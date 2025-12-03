# WP Data Objects

[![Code Style](https://github.com/yardinternet/yard-data/actions/workflows/format-php.yml/badge.svg?no-cache)](https://github.com/yardinternet/yard-data/actions/workflows/format-php.yml)
[![PHPStan](https://github.com/yardinternet/yard-data/actions/workflows/phpstan.yml/badge.svg)](https://github.com/yardinternet/yard-data/actions/workflows/phpstan.yml)
[![Tests](https://github.com/yardinternet/yard-data/actions/workflows/run-tests.yml/badge.svg?no-cache)](https://github.com/yardinternet/yard-data/actions/workflows/run-tests.yml)
[![Code Coverage Badge](https://github.com/yardinternet/yard-data/blob/badges/coverage.svg)](https://github.com/yardinternet/yard-data/actions/workflows/badges.yml)
[![Lines of Code Badge](https://github.com/yardinternet/yard-data/blob/badges/lines-of-code.svg)](https://github.com/yardinternet/yard-data/actions/workflows/badges.yml)

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
wp acorn vendor:publish --provider="Yard\Data\Providers\DataServiceProvider"
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

To do this, you need to add the Fully Qualified Class Name (FQCN) of your custom data class to the `supports` array when registering your custom post type:

```php
register_post_type('vacancy', [
    'supports' => [
        'data-class' => ['classFQN' => App\Data\KnowledgebaseData::class],
    ],
]);
````

Another option is to create a mapping in the `config/yard-data.php` file. The mapping in the project config takes precedence over the register_post_type `supports` args.

```php
'post_types' => [
  'vacancy' => App\Data\VacancyData::class,
  'employee' => App\Data\EmployeeData::class,
```

Now every time you call `Yard\Data\PostData::from($post)` on a custom post type the mapped instance will be returned.

### Meta Fields

#### Using the Meta Attribute

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

You can also specify any available Data Object, and the meta value will be cast to that Data Object:

```php
#[Meta]
public EmployeeData $vacancyEmployee;
```

#### The MetaPrefix Class Attribute

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

It doesn’t matter if your meta keys are in snake_case and your attributes are in camelCase. For instance, let’s say your meta key is `vacancy_members_only`:

```php
#[MetaPrefix(prefix: 'vacancy')]
class VacancyData extends PostData
{
    #[Meta]
    public bool $membersOnly;
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

#### The TaxonomyPrefix Class Attribute

If all of your taxonomies are prefixed with the same prefix you can use the TaxonomyPrefix attribute:

```php
namespace App\Data;

use Illuminate\Support\Collection;
use Yard\Data\Attributes\TaxonomyPrefix;
use Yard\Data\Attributes\Terms;
use Yard\Data\PostData;

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

#### Extending TermData

You can add extra meta fields to taxonomies by extending the default TermData object

```php
namespace App\Data;

use Yard\Data\TermData;

class TypeTermData extends TermData {

  #[Meta()]
  public string $icon;
}

```

In your PostData object you have to specify the data class used for a specific taxonomy:

```php
namespace App\Data;

use Illuminate\Support\Collection;
use Yard\Data\Attributes\TaxonomyPrefix;
use Yard\Data\Attributes\Terms;
use Yard\Data\PostData;

class VacancyData extends PostData
{
    #[Terms(dataClass: TypeTermData::class)]
    /** @var Collection<int, TypeTermData> */
    public Collection $type;
}
```

```php
$vacancyTypeIcon = $vacancyData->type->first()?->icon;
```

### UserData

Create UserData from current user:

```php
$userData = UserData::from(wp_get_current_user());
```

## About us

[![banner](https://raw.githubusercontent.com/yardinternet/.github/refs/heads/main/profile/assets/small-banner-github.svg)](https://www.yard.nl/werken-bij/)
