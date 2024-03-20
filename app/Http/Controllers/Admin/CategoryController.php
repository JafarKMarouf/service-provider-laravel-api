<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        try{
            $category = Category::query()->orderBy('created_at','desc')->get();

            return response()->json([
             'status' => 'success',
             'count' => count($category),
             'category' => $category,
            ],200);
        }
        catch(\Exception $e){
            return response()->json([
                'status'=> 'error',
                'message'=> $e->getMessage(),
                ],500);
            }
    }

    public function store(Request $request)
    {
        try{
            if(auth()->user()->role == 'admin'){
                $validate = Validator::make($request->all(), [
                    'title' => 'required|string|unique:categories,title',
                    'description' => 'string|min:20'
                ]);

                if ($validate->fails()) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => $validate->errors()->first(),
                    ], 403);
                }
                $category = Category::create([
                    'title' => $request->title,
                    'description'=> $request->description
                ]);
                return response()->json([
                    'status' => 'success',
                    'data' => $category,
                    'message' => 'Category is Created Successfully!'
                ],200);
            }
            else{
                return response()->json([
                    'status'=> 'failed',
                    'message'=> 'Permission denied'
                ],401);
            }
        }
        catch(\Exception $e){
            return response()->json([
                'status'=> 'error',
                'message'=> $e->getMessage(),
                ],500);
            }
    }

    public function show(Category $id)
    {
        try{
            $category = Category::find($id);
            if (!$category) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Category not found'
                ], 403);
            }

            return response()->json([
                'status' => 'success',
                'data' => $category,
            ],200);
        }
        catch(\Exception $e){
            return response()->json([
                'status'=> 'error',
                'message'=> $e->getMessage(),
                ],500);
            }
    }
    public function update(Request $request, string $id)
    {
        try{
            if(auth()->user()->role == 'admin'){
                $validate = Validator::make($request->all(), [
                    'title' => 'string|unique:categories,title',
                    'description' => 'string|min:20'
                ]);

                if ($validate->fails()) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => $validate->errors()->first(),
                    ], 403);
                }
                $category = Category::find($id);
                if (!$category) {
                    return response()->json([
                        'status'=> 'failed',
                        'message'=> 'Category not found'
                    ],403);
                }
                $category->update([
                    'title' => $request->title,
                    'description' => $request->description ? $request->description:$category->description,
                ]);

                return response()->json([
                    'status' => 'success',
                    'data' => $category,
                    'message' => 'Category is Updated Successfully!'
                ],200);
            }
            else{
                return response()->json([
                    'status'=> 'failed',
                    'message'=> 'Permission denied'
                ],401);
            }
        }
        catch(\Exception $e){
            return response()->json([
                'status'=> 'error',
                'message'=> $e->getMessage(),
                ],500);
            }
    }

    public function destroy(string $id)
    {
        try{
            if(auth()->user()->role == 'admin'){
                $category = Category::find($id);
                if (!$category) {
                    return response()->json([
                        'status'=> 'failed',
                        'message'=> 'Category not found'
                    ],403);
                }
                $category->delete();

                return response()->json([
                    'status' => 'success',
                    'data'=> [],
                    'message'=> 'Category deleted Succfully!',
                ]);
            }
            else{
                return response()->json([
                    'status'=> 'failed',
                    'message'=> 'Permission denied'
                ],401);
            }
        }
        catch(\Exception $e){
            return response()->json([
                'status'=> 'error',
                'message'=> $e->getMessage(),
                ],500);
            }
    }

    public function search(String $name){
        try{
            // return $name;
            $category = Category::where('title','LIKE','%'.$name.'%')
                ->orWhere('description','LIKE','%'.$name.'%')
                ->get();
            if(empty($category)){
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Category not found',
                ],404);
            }
            else{
                return response()->json([
                    'status'=> 'success',
                    'data' => $category,
                ],200);
            }
            // return ;
        }
        catch(\Exception $e){
            return response()->json([
                'status'=> 'error',
                'message'=> $e->getMessage(),
                ],500);
            }
    }
}
