<?php

declare(strict_types=1);

namespace Yard\Data\Enums;

enum PostStatus: string
{
	case PUBLISH = 'publish';
	case FUTURE = 'future';
	case DRAFT = 'draft';
	case PENDING = 'pending';
	case PRIVATE = 'private';
	case TRASH = 'trash';
	case AUTO = 'auto-draft';
	case INHERIT = 'inherit';

	public function label(): string
	{
		$stati = get_post_stati([], 'objects');

		return isset($stati[$this->value]) ? $stati[$this->value]->label : $this->value;
	}
}
