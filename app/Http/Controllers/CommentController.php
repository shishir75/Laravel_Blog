<?php

namespace App\Http\Controllers;

use App\Comment;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
	public function store(Request $request, $post)
	{
		$input = $request->except('_token');
		$rules = [
			'comment' => 'required'
		];

		$validator = Validator::make($input, $rules);
		if ($validator->fails())
		{
			return redirect()->back()->withErrors($validator)->withInput();
		}

		$comment = new Comment();
		$comment->post_id = $post;
		$comment->user_id = Auth::id();
		$comment->comment = $request->input('comment');
		$comment->save();

		Toastr::success('Comment Successfully Published!', 'Success');
		return redirect()->back();
    }
}
