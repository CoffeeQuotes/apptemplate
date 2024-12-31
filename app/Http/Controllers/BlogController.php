<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function category(BlogCategory $category): View
    {
        $posts = BlogPost::with(['author', 'featuredImage', 'categories'])
            ->whereHas('categories', function ($query) use ($category) {
                $query->where('id', $category->id);
            })
            ->published()
            ->latest('published_at')
            ->paginate(12);

        $categories = BlogCategory::withCount(['posts' => function ($query) {
            $query->published();
        }])->get();

        return view('blog.index', compact('posts', 'categories', 'category'));
    }
} 