<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogPostController extends Controller
{
    public function index(Request $request): View
    {
        $posts = BlogPost::with(['author', 'featuredImage', 'categories'])
            ->published()
            ->latest('published_at')
            ->paginate(12);

        $categories = BlogCategory::withCount('posts')->get();

        return view('blog.index', compact('posts', 'categories'));
    }

    public function show(string $slug): View
    {
        $post = BlogPost::with(['author', 'featuredImage', 'categories', 'tags'])
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        // Add SEO meta data
        $metaTitle = $post->seo_title ?: $post->title;
        $metaDescription = $post->seo_description;
        $metaKeywords = $post->seo_keywords;

        $relatedPosts = BlogPost::published()
            ->whereHas('categories', function ($query) use ($post) {
                $query->whereIn('id', $post->categories->pluck('id'));
            })
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('blog.show', compact('post', 'relatedPosts', 'metaTitle', 'metaDescription', 'metaKeywords'));
    }

    public function preview(BlogPost $post): View
    {
        // Get related posts (similar to show method)
        $relatedPosts = BlogPost::published()
            ->whereHas('categories', function ($query) use ($post) {
                $query->whereIn('id', $post->categories->pluck('id'));
            })
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        // Add SEO meta data
        $metaTitle = $post->seo_title ?: $post->title;
        $metaDescription = $post->seo_description;
        $metaKeywords = $post->seo_keywords;

        return view('blog.show', compact('post', 'relatedPosts', 'metaTitle', 'metaDescription', 'metaKeywords'));
    }
} 