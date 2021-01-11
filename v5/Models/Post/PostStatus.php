<?php
namespace v5\Models\Post;

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
}
