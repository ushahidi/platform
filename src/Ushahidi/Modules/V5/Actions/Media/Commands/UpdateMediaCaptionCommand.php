<?php

namespace Ushahidi\Modules\V5\Actions\Media\Commands;

use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Models\Media;
use Ushahidi\Modules\V5\Requests\MediaRequest;
use Ushahidi\Core\Entity\Media as MediaEntity;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Modules\V5\Helpers\ParameterUtilities;

class UpdateMediaCaptionCommand implements Command
{

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $caption;

    /**
     * @var MediaEntity
     */
    private $media_entity;

    public function __construct(
        string $caption,
        Media $media
    ) {
        // $this->id = $id;
        $input['id'] = $media->id;
        $input['user_id'] = $media->user_id;
        $input['caption'] = $caption;
        $input['mime'] = $media->mime;
        $input['o_filename'] = $media->o_filename;
        $input['o_size'] = $media->o_size;
        $input['o_width'] = $media->o_width;
        $input['o_height'] = $media->o_height;
        $input['created'] = strtotime($media->created);
        $input['updated'] = time();

        $this->media_entity = new MediaEntity($input);
    }

    // public function getId(): int
    // {
    //     return $this->id;
    // }
    public function getCaption(): string
    {
        return $this->caption;
    }

    public function getMediaEntity(): MediaEntity
    {
        return $this->media_entity;
    }
}
