<?php

namespace App\Jobs;

use File;
use Image;
use Storage;
use Throwable;
use App\Models\Design;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UploadImage implements ShouldQueue
{
    use Queueable;
    use Dispatchable;
    use SerializesModels;
    use InteractsWithQueue;

    protected $design;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $retryAfter = 5;

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Design $design)
    {
        $this->design = $design;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $disk = $this->design->disk;
        $fileName = $this->design->image;
        $original_file = storage_path() . '/uploads/original/' . $fileName;


        $image = Image::make($original_file)
            ->fit(800, 600, function ($constraint) {
                $constraint->aspectRatio();
            })
            ->save($large = storage_path() . '/uploads/large/' . $fileName);

        $image = Image::make($original_file)
            ->fit(250, 200, function ($constraint) {
                $constraint->aspectRatio();
            })
            ->save($thumbnail = storage_path() . '/uploads/thumbnail/' . $fileName);

        $originalFile = Storage::disk($disk)
            ->put('/uploads/designs/original/' . $fileName, fopen($original_file, 'r+'));

        if ($originalFile) {
            File::delete($original_file);
        };

        $largeFile = Storage::disk($disk)
            ->put('/uploads/designs/large/' . $fileName, fopen($large, 'r+'));

        if ($largeFile) {
            File::delete($large);
        };

        $thumbnailFile = Storage::disk($disk)
            ->put('/uploads/designs/thumbnail/' . $fileName, fopen($thumbnail, 'r+'));

        if ($thumbnailFile) {
            File::delete($thumbnail);
        };

        $this->design->upload_successful = true;
        $this->design->save();
    }

    /**
     * Handle a job failure.
     *
     * @param \Throwable $exception
     */
    public function failed(Throwable $exception): void
    {
        $this->design->upload_successful = false;
        $this->design->save();
    }
}
