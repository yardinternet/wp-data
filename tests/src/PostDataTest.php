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

	\WP_Mock::userFunction('is_post_publicly_viewable', [
		'args' => [1],
		'return' => true,
	]);

	expect($this->postData->url())->toBe('https://example.com/hello-world');
});

it('returns an empty string when post is not publicly viewable', function () {
	\WP_Mock::userFunction('is_post_publicly_viewable', [
		'args' => [1],
		'return' => false,
	]);

	expect($this->postData->url())->toBe('');
});

it('returns an empty string when url does not exist', function () {
	\WP_Mock::userFunction('get_permalink', [
		'args' => [1],
		'return' => false,
	]);

	\WP_Mock::userFunction('is_post_publicly_viewable', [
		'args' => [1],
		'return' => true,
	]);

	expect($this->postData->url())->toBe('');
});
