<?php

declare(strict_types=1);

beforeEach(function () {
	$this->postData = new Yard\Data\PostData(
		id: 1,
		author: null,
		title: 'Hello, World!',
		content: 'This is a test post.',
		excerpt: 'This is a test excerpt.',
		status: Yard\Data\Enums\PostStatus::PUBLISH,
		date: Carbon\CarbonImmutable::now(),
		modified: Carbon\CarbonImmutable::now(),
		postType: 'post',
		slug: 'hello-world',
		thumbnail: null,
		commentCount: 0,
	);
});

it('can construct PostData', function () {
	expect($this->postData)->toBeInstanceOf(Yard\Data\PostData::class);
});

it('can get the URL of the post', function () {
	\WP_Mock::userFunction('get_permalink', [
		'args' => [1],
		'return' => 'https://example.com/hello-world',
	]);

	\WP_Mock::userFunction('is_post_type_viewable', [
		'args' => ['post'],
		'return' => true,
	]);

	expect($this->postData->url())->toBe('https://example.com/hello-world');
});

it('returns an empty string when post type is not viewable', function () {
	\WP_Mock::userFunction('is_post_type_viewable', [
		'args' => ['post'],
		'return' => false,
	]);

	expect($this->postData->url())->toBe('');
});

it('returns an empty string when url does not exist', function () {
	\WP_Mock::userFunction('get_permalink', [
		'args' => [1],
		'return' => false,
	]);

	\WP_Mock::userFunction('is_post_type_viewable', [
		'args' => ['post'],
		'return' => true,
	]);

	expect($this->postData->url())->toBe('');
});

it('only queries published children', function () {
	\WP_Mock::userFunction('is_post_type_hierarchical', [
		'args' => ['post'],
		'return' => true,
	]);

	\WP_Mock::userFunction('wp_parse_args', [
		'return' => fn (array $args, array $defaults): array => array_merge($defaults, $args),
	]);

	$queried = [];

	\WP_Mock::userFunction('get_children', [
		'return' => function (array $args) use (&$queried): array {
			$queried = $args;

			return [];
		},
	]);

	$this->postData->children();

	expect($queried['post_status'])->toBe('publish');
});

it('lets callers override the child post status', function () {
	\WP_Mock::userFunction('is_post_type_hierarchical', [
		'args' => ['post'],
		'return' => true,
	]);

	\WP_Mock::userFunction('wp_parse_args', [
		'return' => fn (array $args, array $defaults): array => array_merge($defaults, $args),
	]);

	$queried = [];

	\WP_Mock::userFunction('get_children', [
		'return' => function (array $args) use (&$queried): array {
			$queried = $args;

			return [];
		},
	]);

	$this->postData->children(['post_status' => 'any']);

	expect($queried['post_status'])->toBe('any');
});

it('has no parent when the parent is not published', function () {
	\WP_Mock::userFunction('is_post_type_hierarchical', [
		'args' => ['post'],
		'return' => true,
	]);

	\WP_Mock::userFunction('get_post_parent', [
		'args' => [1],
		'return' => (object) ['ID' => 2, 'post_status' => 'draft'],
	]);

	expect($this->postData->parent())->toBeNull();
	expect($this->postData->isChild())->toBeFalse();
});
