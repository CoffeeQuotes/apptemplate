<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\View\View;

class BlogController extends Controller
{
    // ... existing methods ...

    public function category(Category $category): View
    {
        $posts = $category->posts()
            ->published()
            ->with(['author', 'categories', 'featuredImage'])
            ->latest('published_at')
            ->paginate(10);

        $categories = Category::withCount(['posts' => function ($query) {
            $query->published();
        }])->orderBy('name')->get();

        return view('blog.index', compact('posts', 'categories'));
    }
} 