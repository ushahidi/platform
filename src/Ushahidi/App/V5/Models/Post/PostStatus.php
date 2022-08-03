<?php
namespace Ushahidi\App\V5\Models\Post;

class PostStatus
{
    const DRAFT = 'draft';
    const PUBLISHED = 'published';
    const ARCHIVED = 'archived';

    public static function all()
    {
        return [PostStatus::ARCHIVED, PostStatus::DRAFT, PostStatus::PUBLISHED];
    }

    public static function normalize(string $status)
    {
        return in_array(strtolower($status), PostStatus::all()) ? strtolower($status) : null;
    }

    public static function isValidTransition(string $from_status, string $to_status)
    {
        if ($from_status === $to_status) {
            return null;
        } else {
            // We don't have any restrictions really (yet?)
            return true;
        }
    }
}
