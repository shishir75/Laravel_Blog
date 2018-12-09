<?php

namespace App\Http\Controllers;

use App\Subscriber;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubscriberController extends Controller
{
	public function store(Request $request)
	{
		$input = $request->except('_token');
		$rules = [
			'email' => 'required | email | unique:subscribers'
		];
		$validator = Validator::make($input, $rules);
		if ($validator->fails())
		{
			return redirect()->back()->withErrors($validator)->withInput();
		}

		$subscriber = new Subscriber();
		$subscriber->email = $request->input('email');
		$subscriber->save();

		Toastr::success('You are successfully added to our Subscriber list!', 'Success');
		return redirect()->back();

    }
}
