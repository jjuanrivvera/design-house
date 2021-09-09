<?php

namespace App\Http\Controllers\Designs;

use App\Jobs\UploadImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        // return response()->json($request->all());
        $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $image = $request->file('image');
        $image_path = $image->getPathName();

        $fileName = time() . "_" . preg_replace('/\s+/', '_', strtolower($image->getClientOriginalName()));

        $temp = $image->storeAs('uploads/original', $fileName, 'tmp');

        $design = auth()->user()->designs()->create([
            'image' => $fileName,
            'disk' => config('site.upload_disk')
        ]);

        dispatch(new UploadImage($design));

        return response()->json($design, 200);
    }
}
