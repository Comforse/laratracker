<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Categories\CreateCategoryRequest;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Lang;

class CategoriesController extends Controller
{
    /**
     * Create a new category
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    public function createPost(CreateCategoryRequest $request)
    {
        if($request->isMethod('POST')) {
            $name = strip_tags($request->get('category_name'));
            $description = strip_tags($request->get('category_description'));
            $css = strip_tags($request->get('category_css'));

            $result = Category::create([
                'name' => $name,
                'description' => $description,
                'css_class' => $css
            ]);

            if($result !== null)
            {
                return redirect('/admin/categories')->with('status', Lang::get('messages.admin_categories_status_created'));
            }
        }

        return (new Response("Invalid Request", 400))
            ->header('Content-Type', 'text/plain')
            ->header("Pragma", "no-cache");
    }

    public function index()
    {
        $categories = Category::all();
        return view('admin.categories.index', compact('categories'));
    }
}
