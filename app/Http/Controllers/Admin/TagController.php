<?php

namespace App\Http\Controllers\Admin;

use App\Tag;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tags = Tag::latest()->get();
        return view('admin.tag.index', compact('tags'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.tag.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->except('_token');
        $rules = [
            'name' => 'required'
        ];

        $validator = Validator::make($input, $rules);
	    if ($validator->fails()){
		    return redirect()->back()->withErrors($validator)->withInput();
	    }

		$create = Tag::create([
			'name' => $request->input('name'),
			'slug' => str_slug($request->input('name')),
		]);
		if ($create)
		{
			Toastr::success('Tag Successfully Saved', 'Success!!!');
			return redirect()->route('admin.tag.index');
		}


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $tag = Tag::find($id);
        return view('admin.tag.edit', compact('tag'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
	    $input = $request->except('_token');
	    $rules = [
		    'name' => 'required'
	    ];

	    $validator = Validator::make($input, $rules);
	    if ($validator->fails()){
		    return redirect()->back()->withErrors($validator)->withInput();
	    }

        $tag = Tag::find($id);

        $update = $tag->update([
            'name' => $request->input('name'),
            'slug' => str_slug($request->input('name')),
        ]);
        if ($update)
        {
            Toastr::success('Tag Updated Successfully', 'Success!!!');
            return redirect()->route('admin.tag.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Tag::find($id)->delete();
        Toastr::success('Tag Deleted Successfully', 'Success!!');
        return redirect()->back();
    }
}
