<?php

namespace Ushahidi\App\V5\Http\Middleware;

use Closure;
use Ushahidi\App\V5\Models\Stage;
use Ushahidi\App\V5\Models\Category;
use Ushahidi\App\V5\Models\Post\Post;
use Ushahidi\App\V5\Models\PostValues\PostInt;
use Ushahidi\App\V5\Models\PostValues\PostTag;
use Ushahidi\App\V5\Models\PostValues\PostText;
use Ushahidi\App\V5\Models\PostValues\PostMedia;
use Ushahidi\App\V5\Models\PostValues\PostPoint;
use Ushahidi\App\V5\Models\PostValues\PostValue;
use Ushahidi\App\V5\Models\PostValues\PostDecimal;
use Ushahidi\App\V5\Models\PostValues\PostVarchar;
use Ushahidi\App\V5\Models\PostValues\PostDatetime;
use Ushahidi\App\V5\Models\PostValues\PostGeometry;
use Ushahidi\App\V5\Models\PostValues\PostMarkdown;
use Ushahidi\App\V5\Models\PostValues\PostRelation;
use Ushahidi\App\V5\Models\Scopes\StageAllowed;
use Ushahidi\App\V5\Models\Scopes\PostAllowed;
use Ushahidi\App\V5\Models\Scopes\CategoryAllowed;
use Ushahidi\App\V5\Models\Scopes\PostValueAllowed;

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
        $isSavingPost = $request->isMethod('post') &&
            in_array($request->path(), [
                'api/v5/posts',
                'api/v5/posts/_ussd',
                'api/v5/posts/_whatsapp'
            ]);

        if (!$isSavingPost) {
            Category::addGlobalScope(new CategoryAllowed);
            Post::addGlobalScope(new PostAllowed);
            Stage::addGlobalScope(new StageAllowed);
            PostValue::addGlobalScope(new PostValueAllowed);
            PostDatetime::addGlobalScope(new PostValueAllowed);
            PostDecimal::addGlobalScope(new PostValueAllowed);
            PostGeometry::addGlobalScope(new PostValueAllowed);
            PostInt::addGlobalScope(new PostValueAllowed);
            PostMarkdown::addGlobalScope(new PostValueAllowed);
            PostMedia::addGlobalScope(new PostValueAllowed);
            PostPoint::addGlobalScope(new PostValueAllowed);
            PostRelation::addGlobalScope(new PostValueAllowed);
            PostTag::addGlobalScope(new PostValueAllowed);
            PostText::addGlobalScope(new PostValueAllowed);
            PostVarchar::addGlobalScope(new PostValueAllowed);
        }
        return $next($request);
    }
}
