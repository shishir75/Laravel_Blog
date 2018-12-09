<?php

namespace App\Http\Controllers\Author;

use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class SettingsController extends Controller
{
	public function index()
	{
		return view('author.settings');
	}

	public function profileUpdate(Request $request)
	{
		$inputs = $request->except('_token');
		$rules = [
			'name' => 'required',
			'email' => 'required | email',
			'image' => 'required | image'
		];

		$validator = Validator::make($inputs, $rules);
		if ($validator->fails())
		{
			return redirect()->back()->withErrors($validator)->withInput();
		}

		$image = $request->file('image');
		$slug = str_slug($request->input('name'));
		$user = User::findOrFail(Auth::id());

		if (isset($image))
		{
			$currentDate = Carbon::now()->toDateString();
			$image_name = $slug.'-'.$currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();

			if (!Storage::disk('public')->exists('profile'))
			{
				Storage::disk('public')->makeDirectory('profile');
			}

			// delete old image from profile folder
			if (Storage::disk('public')->exists('profile/'.$user->image))
			{
				Storage::disk('public')->delete('profile/'.$user->image);
			}

			// resize the image
			$profile_iamge = Image::make($image)->resize(500,500)->stream();
			Storage::disk('public')->put('profile/'.$image_name, $profile_iamge);
		} else {
			$image_name = $user->image;
		}

		$user->name = $request->input('name');
		$user->email = $request->input('email');
		$user->image = $image_name;
		$user->about = $request->input('about');
		$user->save();

		Toastr::success('Profile Successfully Updated!!', 'Success');
		return redirect()->back();
	}

	public function passwordUpdate(Request $request)
	{
		$inputs = $request->except('_token');
		$rules = [
			'old_password' => 'required',
			'password' => 'required | confirmed',
		];

		$validator = Validator::make($inputs, $rules);
		if ($validator->fails())
		{
			return redirect()->back()->withErrors($validator);
		}

		$old_password = $request->input('old_password');
		$new_password = $request->input('password');
		$hashedPassword = Auth::user()->password;

		if (Hash::check($old_password, $hashedPassword))
		{
			if (!Hash::check($new_password, $hashedPassword))
			{
				$user = User::find(Auth::id());
				$user->password = Hash::make($new_password);
				$user->save();

				Toastr::success('Password Successfully Changed!', 'Success');
				Auth::logout();
				return redirect()->back();
			} else {
				Toastr::error('New Password can not be same as Old Password', 'Error');
				return redirect()->back();
			}
		} else {
			Toastr::error('Current Password not Matched!', 'Error');
			return redirect()->back();
		}

	}



}
