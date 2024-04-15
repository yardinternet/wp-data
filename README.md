# Yard Models

Models for WordPress.

## Installation

You can install this package with Composer:

```bash
composer require yard/models
```

You can publish the config file with:

```shell
$ wp acorn vendor:publish --provider="Yard\Models\Providers\ModelServiceProvider"
```

## Usage

From a Blade template:

```blade
@include('Model::model')
```

From WP-CLI:

```shell
$ wp acorn model
```
