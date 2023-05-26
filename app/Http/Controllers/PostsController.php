<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class PostsController extends Controller
{
    /**
     * Get post list
     * 
     * @param   \Illuminate\Http\Request    $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function getData(Request $request) {
        $posts = Post::orderBy('created_at', 'DESC')->paginate(10);

        return response()->json($posts);
    }

    /**
     * Render the posts lists
     * 
     * @return 
     */
    public function index(): Response {
        return Inertia::render('Posts/Index');
    }

    /**
     * Store new posts
     * 
     * @param   \App\Http\Requests\PostRequest $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function store(PostRequest $request): JsonResponse {
        try {
            $imagePath = $this->storeImage($request->image);

            Post::create([
                'user_id'       => Auth::id(),
                'title'         => $request->title,
                'description'   => $request->description,
                'image'         => 'storage/' . $imagePath
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message'   =>  'ERROR',
                'errors'    => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'message'   => 'OK',
        ], 200);
    }

    /**
     * Store the given image file on local storage
     * 
     * @param   \Illuminate\Http\UploadedFile   $file 
     * @return  string
     */
    private function storeImage(UploadedFile $image): string {
        try {
            $imagePath = 'img/' . Auth::id() . '/posts/' . date('YmdHis') . '.' . $image->extension(); 
            
            Storage::disk('public')->put($imagePath, file_get_contents($image));
        } catch (\Throwable $th) {
            throw $th;
        }

        return $imagePath;
    }
}
