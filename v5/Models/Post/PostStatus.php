<?php
namespace v5\Models\Post;

class PostStatus
{
    const DRAFT = 'draft';
    const PUBLISHED = 'published';
    const ARCHIVED = 'archived';

    public static function all()
    {
        return [self::ARCHIVED, self::DRAFT, self::PUBLISHED];
    }

    public static function normalize(string $status)
    {
        return in_array(strtolower($status), self::all()) ? strtolower($status) : null;
    }
}
