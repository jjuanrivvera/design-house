<?php

namespace App\Jobs;

use File;
use Image;
use Storage;
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

        try {
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
        } catch (\Throwable $th) {
            \Log::error($th->getMessage());
        }
    }
}
