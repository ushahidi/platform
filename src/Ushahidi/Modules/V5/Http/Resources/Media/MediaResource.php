<?php
namespace Ushahidi\Modules\V5\Http\Resources\Media;

use Illuminate\Http\Resources\Json\JsonResource as Resource;
use Ushahidi\Core\Entity\Media as MediaEntity;
use Illuminate\Support\Facades\Storage;


use App\Bus\Query\QueryBus;

class MediaResource extends Resource
{

    // use RequestCachedResource;

    public static $wrap = 'result';
    private function getResourcePrivileges()
    {
        $authorizer = service('authorizer.media');
        // Obtain v3 entity from the v5 post model
        // Note that we use attributesToArray instead of toArray because the first
        // would have the effect of causing unnecessary requests to the database
        // (relations are not needed in this case by the authorizer)
        $entity = new MediaEntity($this->resource->toArray());
        // if there's no user the guards will kick them off already, but if there
        // is one we need to check the authorizer to ensure we don't let
        // users without admin perms create forms etc
        // this is an unfortunate problem with using an old version of lumen
        // that doesn't let me do guest user checks without adding more risk.
        return $authorizer->getAllowedPrivs($entity);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'caption' => $this->caption,
            'mime'=> $this->mime,
            'original_file_url'=> $this->formatOFilename($this->o_filename),
            'original_file_size'=> $this->o_size,
            'original_width'=> $this->o_width,
            'original_height'=> $this->o_height,
            'created'=> $this->created,
            'updated'=> $this->updated,
            'allowed_privileges' => $this->getResourcePrivileges()



        ];
        $data = $this->resource->toArray();
        $data['allowed_privileges'] = $this->getResourcePrivileges();
        return $data;
    }

    protected function formatOFilename($value)
    {
        if (empty($value)) {
            return null;
        }
        return $this->resolveMediaUrl($value);
    }

    /**
     * Resolve the public URL for a media file, trying multiple name-encoding
     * strategies to account for how the file may have been stored historically.
     *
     * Given a DB value like "uploads/some file.jpg", we try:
     *
     *   1st) Raw name:     "uploads/some file.jpg"
     *        → Object in storage is literally named "some file.jpg"
     *        → S3 URL:  https://s3.../uploads/some%20file.jpg  (S3 encodes it)
     *        → Return as-is. The URL is already correct.
     *
     *   2nd) Single-encoded: "uploads/some%20file.jpg"
     *        → Object in storage is literally named "some%20file.jpg"
     *          (old code used rawurlencode before saving to storage)
     *        → CDN URL: https://cdn.../uploads/some%20file.jpg
     *        → Problem: browser decodes %20 → requests "some file.jpg" → 404
     *        → Fix: escape % → %25 so URL becomes .../some%2520file.jpg
     *          browser decodes %25 → "%" and requests "some%20file.jpg" ✓
     *
     *   3rd) Double-encoded: "uploads/some%2520file.jpg"
     *        → Object in storage is literally named "some%2520file.jpg"
     *        → Same browser-decoding issue, same %25 fix applied.
     *
     * @param string $value  The o_filename value from the database
     * @return string|null   The public URL, or null if not found
     */
    protected function resolveMediaUrl($value)
    {
        $url_path = explode("/", $value);
        $filename = array_pop($url_path);

        // 1st try: raw filename as stored in the DB
        // e.g. "some file.jpg" → look for object "some file.jpg"
        $path = implode("/", array_merge($url_path, [$filename]));
        $result = Storage::url($path);
        if (is_string($result) && !empty($result)) {
            // URL from storage is already properly encoded (e.g. S3 returns %20 for spaces)
            return $result;
        }

        // 2nd try: single rawurlencode — for files stored with encoded names
        // e.g. "some file.jpg" → rawurlencode → "some%20file.jpg"
        //   look for object literally named "some%20file.jpg"
        $encodedOnce = rawurlencode($filename);
        $path = implode("/", array_merge($url_path, [$encodedOnce]));
        $result = Storage::url($path);
        if (is_string($result) && !empty($result)) {
            // The object name has literal "%" chars (e.g. "some%20file.jpg").
            // The CDN URL contains those raw %, which browsers would decode.
            // Escape % → %25 so browsers preserve the literal percent sign.
            // e.g. ".../some%20file.jpg" → ".../some%2520file.jpg"
            //   browser decodes %25→% and correctly requests "some%20file.jpg"
            return str_replace('%', '%25', $result);
        }

        // 3rd try: double rawurlencode — for doubly-encoded legacy names
        // e.g. "some%20file.jpg" → rawurlencode → "some%2520file.jpg"
        //   look for object literally named "some%2520file.jpg"
        $encodedTwice = rawurlencode($encodedOnce);
        $path = implode("/", array_merge($url_path, [$encodedTwice]));
        $result = Storage::url($path);
        if (is_string($result) && !empty($result)) {
            // Same %-escaping logic as the 2nd try
            return str_replace('%', '%25', $result);
        }

        return null;
    }
}
