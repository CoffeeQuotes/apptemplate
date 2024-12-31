<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function preview(Page $page): View
    {
        // Check if template exists, otherwise use default
        $template = view()->exists("pages.templates.{$page->template}") 
            ? "pages.templates.{$page->template}"
            : 'pages.templates.default';

        return view($template, compact('page'));
    }

    public function show(string $slug): View
    {
        try {
            \Log::info("Attempting to find page with slug: {$slug}");
            
            $query = Page::where('slug', $slug);
            \Log::info("Base query built");
            
            $query->published();
            \Log::info("Published scope applied");
            
            $page = $query->first();
            \Log::info("Query executed. Page found: " . ($page ? 'yes' : 'no'));
            
            if (!$page) {
                \Log::info("Page query SQL: " . $query->toSql());
                \Log::info("Page query bindings: " . json_encode($query->getBindings()));
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException;
            }

            // Add SEO meta data
            $metaTitle = $page->seo_title ?: $page->title;
            $metaDescription = $page->seo_description;
            $metaKeywords = $page->seo_keywords;

            // Check if template exists, otherwise use default
            $template = view()->exists("pages.templates.{$page->template}") 
                ? "pages.templates.{$page->template}"
                : 'pages.templates.default';

            return view($template, compact('page', 'metaTitle', 'metaDescription', 'metaKeywords'));
        } catch (\Exception $e) {
            \Log::error("Page not found for slug: {$slug}. Error: " . $e->getMessage());
            throw $e;
        }
    }
} 