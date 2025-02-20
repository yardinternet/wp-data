<?php

declare(strict_types=1);

namespace Yard\Data\Contracts;

use Carbon\CarbonImmutable;
use Yard\Data\Enums\PostStatus;
use Yard\Data\ImageData;
use Yard\Data\UserData;

interface PostDataInterface
{
	public function __construct(
		?int $id,
		?UserData $author,
		string $title,
		string $content,
		string $excerpt,
		PostStatus $status,
		?CarbonImmutable $date,
		?CarbonImmutable $modified,
		string $postType,
		string $slug,
		?ImageData $thumbnail,
	);

	public function id(): ?int;
	public function author(): ?UserData;
	public function title(): string;
	public function content(): string;
	public function excerpt(): string;
	public function status(): string;
	public function date(string $format): string;
	public function modified(string $format): string;
	public function postType(): string;
	public function thumbnail(): ?ImageData;
	public function hasThumbnail(): bool;
	public function slug(): string;
}
