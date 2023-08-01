<?php

namespace Ushahidi\Modules\V5\Actions\Media\Commands;

use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Models\Media;
use Ushahidi\Modules\V5\Requests\MediaRequest;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Core\Entity\Media as MediaEntity;
use Ushahidi\Core\Tool\UploadData;
use Ushahidi\Core\Exception\ValidatorException;

class CreateMediaCommand implements Command
{
    /**
     * @var MediaEntity
     */
    private $media_entity;




    public function __construct(MediaEntity $media_entity)
    {
        $this->media_entity = $media_entity;
    }

    public static function fromRequest(MediaRequest $request): self
    {

        $uploader = service('tool.uploader');

        // Upload the file and get the file reference
        $upload = $uploader->upload(new UploadData(self::getFile($request)));

        $user = Auth::user();
        $input['user_id'] = $request->input('user_id') ?? ($user ? $user->id : null);
        $input['caption'] = $request->input('caption') ?? "";
        $input['mime'] = $upload->type;
        $input['o_filename'] = $upload->file;
        $input['o_size'] = $upload->size;
        $input['o_width'] = $upload->width;
        $input['o_height'] = $upload->height;
        $input['created'] = time();
        $input['updated'] = null;

        return new self(new MediaEntity($input));
    }

    protected static function getfile(MediaRequest $request)
    {
        $file = $request->file('file');
        if ($file) {
            // Get the properties of the UploadedFile object
            $fileName = $file->getClientOriginalName(); // Original file name
            $fileExtension = $file->getClientOriginalExtension(); // File extension
            $fileSize = $file->getSize(); // File size in bytes
            $fileMimeType = $file->getMimeType(); // File MIME type
            $fileRealPath = $file->getRealPath(); // Temporary file path

           
            // Now, you can create an array with the extracted information
            $file_array = [
                'name' => $fileName,
                'extension' => $fileExtension,
                'size' => $fileSize,
                'type' => $fileMimeType,
                'tmp_name' => $fileRealPath,
                // You can add more properties as needed
            ];
            return $file_array;
        }
    }

    /**
     * @return MediaEntity
     */
    public function getMediaEntity(): MediaEntity
    {
        return $this->media_entity;
    }
}
