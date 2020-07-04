<?php

namespace v5\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\Model;
use v5\Models\Category;
use v5\Models\Post;
use v5\Models\PostValues\PostValue;
use v5\Models\Scopes\CategoryAllowed;
use v5\Models\Scopes\PostAllowed;
use v5\Models\Scopes\PostValueAllowed;
use v5\Models\Scopes\StageAllowed;
use v5\Models\Stage;

class V5GlobalScopes
{
    public function handle($request, Closure $next)
    {

        $isSaving = $request->isMethod('post');
        $isPosts = $request->path() === 'api/v5/posts';
        if (!$isSaving && !$isPosts) {
            Category::addGlobalScope(new CategoryAllowed);
            Post::addGlobalScope(new PostAllowed);
            Stage::addGlobalScope(new StageAllowed);
            PostValue::addGlobalScope(new PostValueAllowed);
        }
        return $next($request);
    }
}
