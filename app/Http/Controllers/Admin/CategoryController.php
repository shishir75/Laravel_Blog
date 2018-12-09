<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Brian2694\Toastr\Facades\Toastr;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::latest()->get();
        return view('admin.category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.category.create');
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
            'name' => 'required | min:3 | unique:categories',
            'image' => 'required | mimes:jpeg,bmp,png'
        ];
        $validator = Validator::make($inputs, $rules);
        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // get form image
        $image = $request->file('image');
        $slug = str_slug($request->input('name'));
        if (isset($image))
        {
            // make unique name for image
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug.'-'.$currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();

	        // check category dir is exists
	        if (!Storage::disk('public')->exists('category'))
	        {
		        Storage::disk('public')->makeDirectory('category');
	        }

	        // resize image for category and upload
	        $category = Image::make($image)->resize(1600, 479)->stream();
	        Storage::disk('public')->put('category/'.$imagename, $category);

	        // check category slider dir is exists
	        if (!Storage::disk('public')->exists('category/slider'))
	        {
		        Storage::disk('public')->makeDirectory('category/slider');
	        }

	        // resize image for category slider and upload
	        $slider = Image::make($image)->resize(500, 333)->stream();
	        Storage::disk('public')->put('category/slider/'.$imagename, $slider);


        } else {
	        $imagename = 'default.png';
        }

	    $create = Category::create([
		    'name' => $request->input('name'),
		    'slug' => $slug,
		    'image' => $imagename,
	    ]);

	    if ($create)
	    {
		    Toastr::success('Category Successfully Created', 'Success!!!');
		    return redirect()->route('admin.category.index');
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
        $category = Category::find($id);
        return view('admin.category.edit', compact('category'));
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
	    $inputs = $request->except('_token');
	    $rules = [
		    'name' => 'required | min:3',
		    'image' => 'mimes:jpeg,bmp,png'
	    ];
	    $validator = Validator::make($inputs, $rules);
	    if ($validator->fails())
	    {
		    return redirect()->back()->withErrors($validator)->withInput();
	    }

	    // get form image
	    $image = $request->file('image');
	    $slug = str_slug($request->input('name'));

	    $category = Category::find($id);

	    if (isset($image))
	    {
		    // make unique name for image
		    $currentDate = Carbon::now()->toDateString();
		    $imagename = $slug.'-'.$currentDate.'-'.uniqid().'.'.$image->getClientOriginalExtension();

		    // check category dir is exists
		    if (!Storage::disk('public')->exists('category'))
		    {
			    Storage::disk('public')->makeDirectory('category');
		    }

		    // delete old image
		    if (Storage::disk('public')->exists('category/'.$category->image))
		    {
		        Storage::disk('public')->delete('category/'.$category->image);
		    }


		    // resize image for category and upload
		    $category_image = Image::make($image)->resize(1600, 479)->stream();
		    Storage::disk('public')->put('category/'.$imagename, $category_image);

		    // check category slider dir is exists
		    if (!Storage::disk('public')->exists('category/slider'))
		    {
			    Storage::disk('public')->makeDirectory('category/slider');
		    }

		    // delete old image slider
		    if (Storage::disk('public')->exists('category/slider/'.$category->image))
		    {
			    Storage::disk('public')->delete('category/slider/'.$category->image);
		    }

		    // resize image for category slider and upload
		    $slider = Image::make($image)->resize(500, 333)->stream();
		    Storage::disk('public')->put('category/slider/'.$imagename, $slider);


	    } else {
		    $imagename = $category->image;
	    }

	    $update = $category->update([
		    'name' => $request->input('name'),
		    'slug' => $slug,
		    'image' => $imagename,
	    ]);

	    if ($update)
	    {
		    Toastr::success('Category Successfully Updated', 'Success!!!');
		    return redirect()->route('admin.category.index');
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
        $category = Category::find($id);

        // delete category image
        if (Storage::disk('public')->exists('category/'.$category->image))
        {
            Storage::disk('public')->delete('category/'.$category->image);
        }

	    // delete category image slider
	    if (Storage::disk('public')->exists('category/slider/'.$category->image))
	    {
		    Storage::disk('public')->delete('category/slider/'.$category->image);
	    }

	    $category->delete();

	    Toastr::success('Category Deleted Successfully', 'Success!!!');
	    return redirect()->back();
    }
}
