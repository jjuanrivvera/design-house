<?php

namespace App\Http\Controllers\Designs;

use App\Models\Design;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\DesignResource;
use Illuminate\Support\Facades\Storage;
use App\Repositories\Contracts\DesignContract;

class DesignController extends Controller
{
    protected $design;

    public function __construct(DesignContract $design)
    {
        $this->design = $design;
    }

    public function index()
    {
        $designs = $this->design->all();
        return DesignResource::collection($designs);
    }

    public function update(Request $request, Design $design)
    {
        $this->authorize('update', $design);

        $this->validate($request, [
            'title' => ['required','max:255','unique:designs,title,' . $design->id],
            'description' => ['required', 'string', 'min:20', 'max:140'],
            'tags' => ['required']
        ]);

        $design->title = $request->title;
        $design->description = $request->description;
        $design->slug = Str::slug($request->title);
        $design->is_live = !$design->upload_successful ? false : $request->is_live;
        $design->save();

        $design->retag($request->tags);

        return new DesignResource($design);
    }

    public function destroy(Design $design)
    {
        $this->authorize('delete', $design);

        foreach (['thumbnail', 'large', 'original'] as $size) {
            if (Storage::disk($design->disk)->exists("uploads/designs/{$size}/" . $design->image)) {
                Storage::disk($design->disk)->delete("uploads/designs/{$size}/" . $design->image);
            }
        }

        $design->delete();

        return response()->json([], 204);
    }
}
