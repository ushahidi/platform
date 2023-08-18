<?php

namespace Ushahidi\Modules\V5\Actions\Media\Commands;

use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Models\Media;
use Ushahidi\Modules\V5\Requests\MediaRequest;
use Ushahidi\Core\Entity\Media as MediaEntity;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Modules\V5\Helpers\ParameterUtilities;

class UpdateMediaCommand implements Command
{

    /**
     * @var int
     */
    private $id;
    /**
     * @var MediaEntity
     */
    private $media_entity;

    public function __construct(
        int $id,
        MediaEntity $media_entity
    ) {
        $this->id = $id;
        $this->media_entity = $media_entity;
    }

    public static function fromRequest(int $id, MediaRequest $request, Media $current_media): self
    {
        $user = Auth::user();
        if (self::hasPermissionToUpdateUser($user)) {
            $input['user_id'] = $request->has('user_id') ? $request->input('user_id') : $current_media->user_id;
        } else {
            $input['user_id'] = $current_media->user_id;
        }

        $input['caption'] = $request->has('caption') ? $request->input('caption') : $current_media->caption;
        $input['mime'] = $current_media->mime;
        $input['o_filename'] = $current_media->o_filename;
        $input['o_size'] = $current_media->o_size;
        $input['o_width'] = $current_media->o_width;
        $input['o_height'] = $current_media->o_height;
        $input['created'] = strtotime($current_media->created);
        $input['updated'] = time();

        return new self($id, new MediaEntity($input));
    }
    private static function hasPermissionToUpdateUser($user)
    {
        if ($user->role === "admin") {
            return true;
        }
        return false;
    }

    public function getId(): int
    {
        return $this->id;
    }
    /**
     * @return MediaEntity
     */
    public function getMediaEntity(): MediaEntity
    {
        return $this->media_entity;
    }
}
