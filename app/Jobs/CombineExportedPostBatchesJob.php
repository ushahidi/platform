<?php

namespace Ushahidi\App\Jobs;

use RuntimeException;
use Log;
use Ushahidi\Core\Entity\ExportJob;
use Ushahidi\Core\Entity\ExportJobRepository;
use Ushahidi\Core\Entity\ExportBatchRepository;
use Illuminate\Http\File;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File as LocalFilesystem;

class CombineExportedPostBatchesJob extends Job
{
    use RecordsExportJobFailure;

    protected $jobId;
    protected $csvPrefix;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($jobId)
    {
        $this->jobId = $jobId;
        $this->csvPrefix = config('media.csv_final_prefix', 'csv');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        ExportJobRepository $exportJobRepo,
        ExportBatchRepository $exportBatchRepo
    ) {
        // Load job
        $job = $exportJobRepo->get($this->jobId);

        if ($job->isCombineBatchesDone()) {
            Log::debug('Job already completed', ['jobId' => $this->jobId]);
            // All done here
            return;
        }

        // Check if all batches exported
        if (!$exportJobRepo->areBatchesFinished($this->jobId)) {
            Log::debug('Batches not finished, requeueing', ['jobId' => $this->jobId]);
            // Batches aren't done yet, wait 5 minutes and try again
            dispatch(
                (new CombineExportedPostBatchesJob($this->jobId))
                    ->delay(Carbon::now()->addMinutes(5))
            );
            // All done
            return;
        }

        // Load batches
        $batches = $exportBatchRepo->getByJobId($this->jobId);
        // Get just filenames
        $fileNames = $batches
            ->sortBy('batch_number')
            ->sortByDesc('has_header')
            ->pluck('filename')
            ->all();

        if (!$this->doAllFilesExist($fileNames)) {
            Log::warning('Some files in export job do not exist', ['jobId' => $this->jobId]);
            throw new RuntimeException('Some files in export job do not exist');
        }

        // Combine the files
        $destinationFile = $this->combineFiles($fileNames);

        // Clean up partial files
        Storage::delete($fileNames);

        // Set status = completed
        $job->setState([
            'url' => $destinationFile, // No longer actually saving a URL, we can format it when it goes to the API
            'status' => ExportJob::STATUS_EXPORTED_TO_CDN
        ]);
        $exportJobRepo->update($job);
    }

    protected function combineFiles(array $fileNames)
    {
        // Create destination filename
        $destinationFileName = Carbon::now()->format('Ymd') .'-'. Str::random(40) . '.csv';
        $tempFile = storage_path('app/temp/' . $destinationFileName);

        foreach ($fileNames as $file) {
            Log::debug("Processing file", compact('file'));
            // Read file into stream
            $stream = Storage::readStream($file);

            // Append to combined file (uses stream automatically)
            LocalFilesystem::append($tempFile, $stream);
        }

        $uploadedFileName = Storage::putFileAs($this->csvPrefix, new File($tempFile), $destinationFileName);

        Log::debug("Uploaded combined file", compact('uploadedFileName'));

        // Destroy local tmp file
        LocalFilesystem::delete($tempFile);

        return $uploadedFileName;
    }

    public function doAllFilesExist(Array $fileNames)
    {
        foreach ($fileNames as $file) {
            if (!Storage::exists($file)) {
                return false;
            }
        }

        return true;
    }
}
