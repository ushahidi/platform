<?php

namespace Ushahidi\Modules\V5\Http\Middleware;

use Closure;
use Ushahidi\Modules\V5\Models\Stage;
use Ushahidi\Modules\V5\Models\Category;
use Ushahidi\Modules\V5\Models\Post\Post;
use Ushahidi\Modules\V5\RepositoryService;
use Ushahidi\Modules\V5\Scopes\PostAllowed;
use Ushahidi\Modules\V5\Scopes\StageAllowed;
use Ushahidi\Modules\V5\Scopes\CategoryAllowed;
use Ushahidi\Modules\V5\Scopes\PostValueAllowed;
use Ushahidi\Modules\V5\Models\PostValues\PostInt;
use Ushahidi\Modules\V5\Models\PostValues\PostTag;
use Ushahidi\Modules\V5\Models\PostValues\PostText;
use Ushahidi\Modules\V5\Models\PostValues\PostMedia;
use Ushahidi\Modules\V5\Models\PostValues\PostPoint;
use Ushahidi\Modules\V5\Models\PostValues\PostValue;
use Ushahidi\Modules\V5\Models\PostValues\PostDecimal;
use Ushahidi\Modules\V5\Models\PostValues\PostVarchar;
use Ushahidi\Modules\V5\Models\PostValues\PostDatetime;
use Ushahidi\Modules\V5\Models\PostValues\PostGeometry;
use Ushahidi\Modules\V5\Models\PostValues\PostMarkdown;
use Ushahidi\Modules\V5\Models\PostValues\PostRelation;

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

        RepositoryService::resolveRepositoryBinder();

        if (!$isSavingPost) {
            Category::addGlobalScope(resolve(CategoryAllowed::class));
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
