<?php

namespace Ushahidi\Modules\V5\Actions\CSV\Commands;

use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Models\CSV;
use Ushahidi\Modules\V5\Requests\CSVRequest;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Core\Entity\CSV as CSVEntity;
use Ushahidi\Modules\V5\Models\Stage;
use Ushahidi\Core\Tool\Uploader;
use Ushahidi\Core\Tool\UploadData;
use SplTempFileObject;
use Ushahidi\Core\Tool\FileReader\CSVReaderFactory;

class CreateCSVCommand implements Command
{
    /**
     * @var CSVEntity
     */
    private $csv_entity;

        /**
     * @var Uploader
     */
    protected $uploader;

    /**
     * @var UploadData
     */
    protected $upload;

    

    // public function __construct(CSVEntity $csv_entity)
    // {
    //     $this->csv_entity = $csv_entity;
    // }

    public function __construct(CSVEntity $csv_entity)
    {
        $this->csv_entity = $csv_entity;
    }

    public static function fromRequest(CSVRequest $request): self
    {
       // $uploader = app()->make('Ushahidi\Core\Tool\Uploader');
        $uploader = service('tool.uploader');
            //dd($request->file('file'));
        $upload_data = new UploadData(self::getFile($request));
        $reader_factory = new CSVReaderFactory();

        // Upload the file and get the file reference
        $upload = $uploader->upload($upload_data);
        // Get SplFileObject for the CSV Reader
        $file = new SplTempFileObject();

        $stream = fopen($upload_data->tmp_name, 'r+');
        $file->fwrite(stream_get_contents($stream));

        // Create a reader and fetch CSV columns
        $reader = $reader_factory->createReader($file);
        $columns = $reader->fetchOne();

        $input['columns'] = $columns;
        $input['size'] = $upload->size;
        $input['mime'] = $upload->type;
        $input['filename'] = $upload->file;

        $input['maps_to'] = null;
        $input['fixed'] = null;
        $input['status'] = null;
        $input['errors'] = null;
        $input['processed'] = null;
        $input['collection_id'] = null;
        
        $input['created'] = time();
        $input['updated'] = null;

        return new self(new CSVEntity($input));
    }

    protected static function getfile(CSVRequest $request)
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
     * @return CSVEntity
     */
    public function getCSVEntity(): CSVEntity
    {
        return $this->csv_entity;
    }
}
