<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Notifications\AuthorPostApproved;
use App\Notifications\NewPostNotify;
use App\Post;
use App\Subscriber;
use App\Tag;
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
        $posts = Post::latest()->get();
        return view('admin.post.index', compact('posts'));
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
        return view('admin.post.create', compact('categories', 'tags'));
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
	    $post->is_approved = true;
	    $post->save();

	    $post->categories()->attach($request->categories);
	    $post->tags()->attach($request->tags);

	    // notify subscribers when new post created by admin
	    $subscribers = Subscriber::all();
	    foreach ($subscribers as $subscriber)
	    {
	        Notification::route('mail', $subscriber->email)->notify(new NewPostNotify($post));
	    }

	    Toastr::success('Post Successfully Created', 'Success!!!');
	    return redirect()->route('admin.post.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return view('admin.post.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
	    $categories = Category::all();
	    $tags = Tag::all();
	    return view('admin.post.edit', compact('post','categories', 'tags'));
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
	    $post->is_approved = true;
	    $post->save();

	    $post->categories()->sync($request->categories);
	    $post->tags()->sync($request->tags);

	    Toastr::success('Post Successfully Updated', 'Success!!!');
	    return redirect()->route('admin.post.index');
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

	public function pending()
	{
		$posts = Post::where('is_approved', false)->latest()->get();
		return view('admin.post.pending', compact('posts'));
    }

	public function approval($id)
	{
		$post = Post::find($id);
		if ($post->is_approved == false)
		{
			$post->is_approved = true;
			$post->save();

			// notify author when post is approved
			$post->user->notify(new AuthorPostApproved($post));

			// notify subscribers when new post is approved by admin
			$subscribers = Subscriber::all();
			foreach ($subscribers as $subscriber)
			{
				Notification::route('mail', $subscriber->email)->notify(new NewPostNotify($post));
			}

			Toastr::success('Post Successfully Approved!', 'Success');
		}else{
			Toastr::info('This post is already been Approved!', 'Info');
		}
		return redirect()->back();
    }




}
