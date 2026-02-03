<?php

declare(strict_types=1);

namespace Yard\Data;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;

/** @phpstan-consistent-constructor */
class CommentData extends Data
{
	public function __construct(
		public int $id,
		public ?PostData $post,
		public string $author,
		public string $authorEmail,
		public string $authorUrl,
		public string $authorIp,
		public ?CarbonImmutable $date,
		public ?CarbonImmutable $dateGmt,
		public string $content,
		public bool $approved,
		public string $agent,
		public string $type,
		public ?CommentData $parent,
		public ?UserData $user,
	) {
	}

	public static function fromComment(\WP_Comment $comment): static
	{
		return new static(
			id: (int) $comment->comment_ID,
			post: null !== get_post((int)$comment->comment_post_ID) ? PostData::fromPost(get_post((int)$comment->comment_post_ID)) : null,
			author: $comment->comment_author,
			authorEmail: $comment->comment_author_email,
			authorUrl: $comment->comment_author_url,
			authorIp: $comment->comment_author_IP,
			date: CarbonImmutable::createFromFormat('Y-m-d H:i:s', $comment->comment_date)?: null,
			dateGmt: CarbonImmutable::createFromFormat('Y-m-d H:i:s', $comment->comment_date_gmt)?: null,
			content: $comment->comment_content,
			approved: (bool) $comment->comment_approved,
			agent: $comment->comment_agent,
			type: $comment->comment_type,
			parent: null !== get_comment((int) $comment->comment_parent) ? CommentData::fromComment(get_comment((int) $comment->comment_parent)) : null,
			user: false !== get_userdata((int) $comment->user_id) ? UserData::fromUser(get_userdata((int) $comment->user_id)) : null,
		);
	}
}
