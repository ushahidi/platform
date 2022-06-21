<?php

namespace Ushahidi\App\Jobs;

use League\Flysystem\Filesystem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use Ushahidi\Core\Entity\MediaRepository;
use Ushahidi\App\Multisite\MultisiteManager;
use Ushahidi\Core\Tool\Uploader;
use Ushahidi\Core\Tool\UploadData;

class ImportMediaJob extends Job
{

    protected $mediaIds;
    protected $srcStorage;
    protected $mediaRepository;
    protected $uploader;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        array $mediaIds,
        string $srcStorage = 'default'
    ) {
        $this->mediaIds = $mediaIds;
        $this->srcStorage = $srcStorage;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        MediaRepository $mediaRepository,
        MultiSiteManager $multisite
    ) {
        $filesystem = service('tool.filesystem');
        $this->mediaRepository = $mediaRepository;
        $this->uploader = new Uploader($filesystem, $multisite);

        //
        foreach ($this->mediaIds as $mediaId) {
            Log::info('Import media with id', [$mediaId]);
            $this->importMediaItem($mediaId);
        }
    }

    protected function importMediaItem(int $mediaId)
    {
        // Obtain media entity
        $media = $this->mediaRepository->get($mediaId);
        if (!$media->getId()) {
            return;
        }
        
        // Get and parse URL
        $url = $media->o_filename;
        $parsed_url = parse_url($url);

        // Download media into temporary file
        $tmppath = tempnam(sys_get_temp_dir(), 'tmpmedia');
        // From an absolute URL, we just use fopen()
        if ($parsed_url['scheme'] ?? null) {
            file_put_contents($tmppath, fopen($url, 'r'));
        } elseif ($this->srcStorage == 'default') {
            // We use the source filesystem driver
            file_put_contents($tmppath, Storage::readStream($url));
        } else {
            file_put_contents($tmppath, Storage::disk($this->srcStorage)->readStream($url));
        }

        // Upload to current storage
        try {
            $upload = null;
            $filename = basename($parsed_url['path']);

            $data = new UploadData([
                'name' => $filename,
                'type' => $media->mime,
                'size' => $media->o_size,
                'tmp_name' => $tmppath
            ]);
            
            $upload = $this->uploader->upload($data, $filename);
        } catch (\InvalidArgumentException $e) {
            Log::error("Error uploading upload ", [
                'filename' => $filename,
                'error' => $e
            ]);
        } finally {
            // remove $tmppath
            unlink($tmppath);
        }

        if ($upload != null) {
            // Update database record
            Log::info("Uploaded file to URL", [
                'filename' => $filename,
                'url' => $upload->file
            ]);

            $payload = [
                'o_filename' => $upload->file,
            ];

            $media->setState($payload);
            $this->mediaRepository->update($media);
        }
    }
}
