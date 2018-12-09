<?php

namespace App\Http\Controllers\Author;

use App\Category;
use App\Notifications\NewAuthorPost;
use App\Tag;
use App\Post;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
	    $posts = Auth::User()->posts()->latest()->get();
	    return view('author.post.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
	    $categories = Category::all();
	    $tags = Tag::all();
	    return view('author.post.create', compact('categories', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
	    $inputs = $request->except('_token');
	    $rules = [
		    'title' => 'required | min:5',
		    'image' => 'required | image',
		    'categories' => 'required',
		    'tags' => 'required',
		    'body' => 'required',
	    ];

	    $validator = Validator::make($inputs, $rules);
	    if ($validator->fails())
	    {
		    return redirect()->back()->withErrors($validator)->withInput();
	    }

	    $image = $request->file('image');
	    $slug = str_slug($request->input('title'));
	    if (isset($image))
	    {
		    $currentDate = Carbon::now()->toDateString();
		    $imageName = $slug.'-'.$currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();

		    if (!Storage::disk('public')->exists('post'))
		    {
			    Storage::disk('public')->makeDirectory('post');
		    }

		    $postImage = Image::make($image)->resize(1600, 1066)->stream();
		    Storage::disk('public')->put('post/'.$imageName, $postImage);
	    } else
	    {
		    $imageName = 'default.png';
	    }

	    $post = new Post();

	    $post->user_id = Auth::id();
	    $post->title = $request->input('title');
	    $post->slug = $slug;
	    $post->image = $imageName;
	    $post->body = $request->input('body');
	    if ($request->input('status') !== null)
	    {
		    $post->status = true;
	    }else{
		    $post->status = false;
	    }
	    $post->is_approved = false;
	    $post->save();

	    $post->categories()->attach($request->categories);
	    $post->tags()->attach($request->tags);

	    // sending notification to admin for new author post
	    $users = User::where('role_id', '1')->get();
	    Notification::send($users, new NewAuthorPost($post));

	    Toastr::success('Post Successfully Created', 'Success!!!');
	    return redirect()->route('author.post.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
	    // check for specific post for specific user to access
        if ($post->user_id != Auth::id())
        {
            Toastr::error('You are not Authorized to access that post', 'Error');
            return redirect()->back();
        }
	    return view('author.post.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        // check for specific post for specific user to access
	    if ($post->user_id != Auth::id())
	    {
		    Toastr::error('You are not Authorized to access that post', 'Error');
		    return redirect()->back();
	    }

	    $categories = Category::all();
	    $tags = Tag::all();
	    return view('author.post.edit', compact('post','categories', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
	    // check for specific post for specific user to access
	    if ($post->user_id != Auth::id())
	    {
		    Toastr::error('You are not Authorized to access that post', 'Error');
		    return redirect()->back();
	    }

	    $inputs = $request->except('_token');
	    $rules = [
		    'title' => 'required | min:5',
		    'image' => 'image',
		    'categories' => 'required',
		    'tags' => 'required',
		    'body' => 'required',
	    ];

	    $validator = Validator::make($inputs, $rules);
	    if ($validator->fails())
	    {
		    return redirect()->back()->withErrors($validator)->withInput();
	    }

	    $image = $request->file('image');
	    $slug = str_slug($request->input('title'));
	    if (isset($image))
	    {
		    $currentDate = Carbon::now()->toDateString();
		    $imageName = $slug.'-'.$currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();

		    if (!Storage::disk('public')->exists('post'))
		    {
			    Storage::disk('public')->makeDirectory('post');
		    }

		    // delete old post photo
		    if (Storage::disk('public')->exists('post/'.$post->image))
		    {
			    Storage::disk('public')->delete('post/'.$post->image);
		    }

		    $postImage = Image::make($image)->resize(1600, 1066)->stream();
		    Storage::disk('public')->put('post/'.$imageName, $postImage);
	    } else
	    {
		    $imageName = $post->image;
	    }

	    $post->user_id = Auth::id();
	    $post->title = $request->input('title');
	    $post->slug = $slug;
	    $post->image = $imageName;
	    $post->body = $request->input('body');
	    if ($request->input('status') !== null)
	    {
		    $post->status = true;
	    }else{
		    $post->status = false;
	    }
	    $post->is_approved = false;
	    $post->save();

	    $post->categories()->sync($request->categories);
	    $post->tags()->sync($request->tags);

	    Toastr::success('Post Successfully Updated', 'Success!!!');
	    return redirect()->route('author.post.index');
    }

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Post $post
	 * @return \Illuminate\Http\Response
	 * @throws \Exception
	 */
    public function destroy(Post $post)
    {
	    // check for specific post for specific user to access
	    if ($post->user_id != Auth::id())
	    {
		    Toastr::error('You are not Authorized to access that post', 'Error');
		    return redirect()->back();
	    }

	    if (Storage::disk('public')->exists('post/'.$post->image))
	    {
		    Storage::disk('public')->delete('post/'.$post->image);
	    }

	    $post->categories()->detach(); // delete categories from category_post pivot table
	    $post->tags()->detach(); // delete tags from post_tag pivot table

	    $post->delete(); // delete post from post table

	    Toastr::success('Post Successfully Deleted!', 'Success');
	    return redirect()->back();
    }
}
