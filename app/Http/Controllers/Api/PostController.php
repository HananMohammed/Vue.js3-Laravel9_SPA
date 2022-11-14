<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Resources\postResource;
use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        $orderColumn = request('order_column', 'created_at');
        if (!in_array($orderColumn, ['id', 'title', 'created_at'])) {
            $orderColumn = 'created_at';
        }
        $orderDirection = request('order_direction', 'desc');
        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }
        $posts = Post::with('category')
                    ->when(request('category'), function ($q) {
                        $q->where('category_id', request('category'));
                    })
                    ->orderBy($orderColumn, $orderDirection)
                    ->paginate(2);
        return PostResource::collection($posts);
    }

    public function store(StorePostRequest $request)
    {
        if ($request->has('thumbnail')) {
            $fileName = $request->file('thumbnail')->getClientOriginalExtension();
            info($fileName);
        }
        $post = Post::create($request->validated());
        return new PostResource($post);
    }

    public function update(Post $post, StorePostRequest $request)
    {
        if ($request->has('thumbnail')) {
            $fileName = $request->file('thumbnail')->getClientOriginalExtension();
            info($fileName);
        }
     //   dd($request->all());
        $post->update($request->validated());
        return new PostResource($post);
    }

    public function show(Post $post)
    {
        return new PostResource($post);
    }
}
