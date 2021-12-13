<?php

namespace App\Http\Controllers\Designs;

use App\Models\Design;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\DesignResource;
use Illuminate\Support\Facades\Storage;
use App\Repositories\Contracts\DesignContract;
use App\Repositories\Eloquent\Criteria\IsLive;
use App\Repositories\Eloquent\Criteria\ForUser;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use App\Repositories\Eloquent\Criteria\LatestFirst;

class DesignController extends Controller
{
    protected $design;

    public function __construct(DesignContract $design)
    {
        $this->design = $design;
    }

    public function index()
    {
        $designs = $this->design->withCriteria(
            new LatestFirst(),
            new IsLive(),
            // new ForUser(auth()->id()),
            new EagerLoad(['user', 'comments'])
        )->paginate();

        return DesignResource::collection($designs);
    }

    public function show($id)
    {
        $design = $this->design->find($id);
        return new DesignResource($design);
    }

    public function update(Request $request, $id)
    {
        $design = $this->design->find($id);
        $this->authorize('update', $design);

        $this->validate($request, [
            'title' => ['required','max:255','unique:designs,title,' . $design->id],
            'description' => ['required', 'string', 'min:20', 'max:140'],
            'tags' => ['required'],
            'team' => ['required_if:assign_to_team,true']
        ]);

        $design = $this->design->update($id, [
            'team_id' => $request->team,
            'title' => $request->title,
            'description' => $request->description,
            'slug' => Str::slug($request->title),
            'is_live' => !$design->upload_successful ? false : $request->is_live
        ]);

        $this->design->applyTags($id, $request->tags);

        return new DesignResource($design);
    }

    public function destroy($id)
    {
        $design = $this->design->find($id);
        $this->authorize('delete', $design);

        foreach (['thumbnail', 'large', 'original'] as $size) {
            if (Storage::disk($design->disk)->exists("uploads/designs/{$size}/" . $design->image)) {
                Storage::disk($design->disk)->delete("uploads/designs/{$size}/" . $design->image);
            }
        }

        $this->design->delete($id);

        return response()->json([], 204);
    }

    public function like($id)
    {
        $this->design->like($id);

        return response()->json([
            "message" => "Successful"
        ], 200);
    }

    public function checkIfUserHasLiked($id)
    {
        return response()->json([
            "liked" => $this->design->isLikedByUser($id)
        ]);
    }

    public function search(Request $request)
    {
        $designs = $this->design->search($request);
        return DesignResource::collection($designs);
    }

    public function findBySlug($slug)
    {
        $design = $this->design->withCriteria([
            new IsLive(),
            new EagerLoad(['user', 'comments'])
        ])->findWhereFirst('slug', $slug);
        return new DesignResource($design);
    }

    public function getForTeam($teamId)
    {
        $designs = $this->design
            ->withCriteria([
                new IsLive()
            ])->findWhere('team_id', $teamId);
        return DesignResource::collection($designs);
    }

    public function getForUser($userId)
    {
        $designs = $this->design
            //->withCriteria([new IsLive()])
            ->findWhere('user_id', $userId);
        return DesignResource::collection($designs);
    }

    public function userOwnsDesign($id)
    {
        $design = $this->design->withCriteria([
            new ForUser(auth()->id())
        ])->findWhereFirst('id', $id);

        return new DesignResource($design);
    }
}
