<?php

namespace App\Http\Controllers\admin;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Unique;

class SubCategoryController extends Controller
{

    public function index(Request $request) {
        $subCategories = SubCategory::select('sub_categories.*', 'categories.name as categoryName')
        ->latest('sub_categories.id')
        ->leftJoin('categories', 'categories.id', 'sub_categories.category_id');

        if (!empty($request->get('keyword'))) {
            $subCategories = $subCategories->where('sub_categories.name', 'like', '%'.$request->get('keyword').'%');
            $subCategories = $subCategories->orwhere('categories.name', 'like', '%'.$request->get('keyword').'%');
        }

        $subCategories = $subCategories->paginate(10);

        return view('admin.sub_category.list',compact('subCategories'));
    }



public function create() {
    $categories = Category::orderBy('name', 'ASC')->get();
    $data['categories'] = $categories;    
    return view('admin.sub_category.create',$data);
}

public function store(Request $request) {
    $validator = Validator::make($request->all(),[
        'name' => 'required',
        'slug' => 'required|unique:sub_categories',
        'category' => 'required',
        'status' => 'required'
    ]);

    if ($validator->passes()) {
        
        $subCategory = new SubCategory();
        $subCategory->name = $request->name;
        $subCategory->slug = $request->slug;
        $subCategory->status = $request->status;
        $subCategory->category_id = $request->category;
        $subCategory->save();
 
        $request->session()->flash('success', 'Sub Category created successfully.');

        return response([
            'status' => true,
           'message' => 'Sub Category created successfully.'
        ]);
    

    } else {
        return response([
            'status' => false,
            'errors' => $validator->errors()
        ]);
    }





}

public function edit($id, Request $request){
  $subCategories = SubCategory::find($id);
  if (empty($subCategories)){
    $request->session()->flash('error', 'Record not found');
    return redirect()->route('sub-categories.index');
  }


    $categories = Category::orderBy('name', 'ASC')->get();
    $data['categories'] = $categories; 
    $data['subCategory'] = $subCategories;  
    return view('admin.sub_category.edit',$data);

}

public function update($id, Request $request){

    $subCategories = SubCategory::find($id);

  if (empty($subCategories)){
    $request->session()->flash('error', 'Record not found');

    return response([
        'status' => false,
        'notfound' => true
    ]);
    // return redirect()->route('sub-categories.index');
  }





    $validator = Validator::make($request->all(),[
        'name' => 'required',
        // 'slug' => 'required|unique:sub_categories',
        'slug' => 'required|unique:sub_categories,slug,'.$subCategories->id.',id',
        'category' => 'required',
        'status' => 'required'
    ]);

    if ($validator->passes()) {
        
        
        $subCategories->name = $request->name;
        $subCategories->slug = $request->slug;
        $subCategories->status = $request->status;
        $subCategories->category_id = $request->category;
        $subCategories->save();
 
        $request->session()->flash('success', 'Sub Category updated successfully.');

        return response([
            'status' => true,
           'message' => 'Sub Category updated successfully.'
        ]);
    

    } else {
        return response([
            'status' => false,
            'errors' => $validator->errors()
        ]);
    }

   




}

public function destroy($id, Request $request){
    $subCategory = SubCategory::find($id);

    if (empty($subCategory)){
        $request->session()->flash('error', 'Record not found');
        return response([
            'status' => false,
            'notFound' => true
        ]);
    }


$subCategory->delete();

$request->session()->flash('success', 'Sub Category deleted sucessfully.');

return response([
    'status' => true,
    'message' => 'Sub Category deleted successfully.'
]);



}

}
