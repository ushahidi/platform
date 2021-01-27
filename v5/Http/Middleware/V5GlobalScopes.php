<?php

namespace v5\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\Model;
use v5\Models\Category;
use v5\Models\Post\Post;
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
        /**
         * The ONLY scenario where we don't attach this scopes out of the box
         * has to be on post saving. This is because during post saving we are not deciding
         * if we need or don't need to show post values, we are deciding only if we can save them
         * and the response is valid to show to that particular POST request.
         * @TODO more tests maybe????????
         * @TODO remove the need for isSavingPost
         */
        $isSavingPost = $request->path() === 'api/v5/posts' &&  $request->isMethod('post');

        if (!$isSavingPost) {
            Category::addGlobalScope(new CategoryAllowed);
            Post::addGlobalScope(new PostAllowed);
            Stage::addGlobalScope(new StageAllowed);
            PostValue::addGlobalScope(new PostValueAllowed);
            \v5\Models\PostValues\PostDatetime::addGlobalScope(new PostValueAllowed);
            \v5\Models\PostValues\PostDecimal::addGlobalScope(new PostValueAllowed);
            \v5\Models\PostValues\PostGeometry::addGlobalScope(new PostValueAllowed);
            \v5\Models\PostValues\PostInt::addGlobalScope(new PostValueAllowed);
            \v5\Models\PostValues\PostMarkdown::addGlobalScope(new PostValueAllowed);
            \v5\Models\PostValues\PostMedia::addGlobalScope(new PostValueAllowed);
            \v5\Models\PostValues\PostPoint::addGlobalScope(new PostValueAllowed);
            \v5\Models\PostValues\PostRelation::addGlobalScope(new PostValueAllowed);
            \v5\Models\PostValues\PostTag::addGlobalScope(new PostValueAllowed);
            \v5\Models\PostValues\PostText::addGlobalScope(new PostValueAllowed);
            \v5\Models\PostValues\PostVarchar::addGlobalScope(new PostValueAllowed);
        }
        return $next($request);
    }
}
