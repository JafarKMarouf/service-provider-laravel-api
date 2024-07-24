<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        try {
            $category = Category::query()
                ->orderBy('created_at', 'desc')
                ->get(['id', 'title', 'description', 'photo']);

            return response()->json([
                'status' => 'success',
                'count' => count($category),
                'data' => $category,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            if (auth()->user()->role == 'admin') {
                $validate = Validator::make($request->all(), [
                    'title' => 'required|string|unique:categories,title',
                    'description' => 'string|min:20',
                    'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                ]);

                if ($validate->fails()) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => $validate->errors()->first(),
                    ], 403);
                }

                $ext = $request->file('photo')->getClientOriginalExtension();
                $filename = $this->saveImage($request->photo, $ext, 'categories');

                $category = Category::create([
                    'title' => $request->title,
                    'description' => $request->description,
                    'photo' => $filename,
                ]);
                return response()->json([
                    'status' => 'success',
                    'data' => $category,
                    'message' => 'Category is Created Successfully!'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Permission denied'
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $category_id = Category::where('id', $id)->count();
            if (!$category_id) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Category not found'
                ], 403);
            }
            $category = Category::query()
                ->where('id', $id)
                ->get(['id', 'title', 'description', 'photo']);

            return response()->json([
                'status' => 'success',
                'data' => $category,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function update(Request $request, string $id)
    {

        try {
            if (auth()->user()->role == 'admin') {
                $validate = Validator::make($request->all(), [
                    'title' => 'string|unique:categories,title',
                    'description' => 'string|min:20',
                    'photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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
                        'status' => 'failed',
                        'message' => 'Category not found'
                    ], 403);
                }
                $filename = '';
                if ($request->hasFile('photo')) {
                    $ext = $request->file('photo')->getClientOriginalExtension();
                    $filename = $this->saveImage($request->photo, $ext, 'categories');
                }

                $category->update([
                    'title' => $request->title != null ? $request->title : $category->title,
                    'description' => $request->description ?? $category->description,
                    'photo' =>  $filename != '' ? $filename : $category->photo,
                ]);

                return response()->json([
                    'status' => 'success',
                    'data' => $category,
                    'message' => 'Category is Updated Successfully!'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Permission denied'
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function destroy(string $id)
    {
        try {
            if (auth()->user()->role == 'admin') {
                $category = Category::find($id);
                if (!$category) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Category not found'
                    ], 403);
                }
                $category->delete();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Category deleted Succfully!',
                ]);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Permission denied'
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function search(String $name)
    {
        try {
            $category = Category::where('title', 'LIKE', '%' . $name . '%')
                ->orWhere('description', 'LIKE', '%' . $name . '%')
                ->get(['title', 'description', 'photo']);

            if ($category->count() > 0) {
                return response()->json([
                    'status' => 'success',
                    'count' => count($category),
                    'data' => $category,
                ], 200);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Category not found',
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
