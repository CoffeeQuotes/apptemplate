<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;

class GalleryPreviewController extends Controller
{
    public function show(Request $request, Gallery $gallery)
    {
        // Set default template if none exists
        if (empty($gallery->template)) {
            $gallery->template = 'grid';
            $gallery->save();
        }

        if ($request->has('template')) {
            $gallery->template = $request->template;
            $gallery->save();
        }

        $settings = $gallery->settings ?? [];
        if ($request->has('columns')) {
            $settings['columns'] = (int) $request->columns;
            $gallery->settings = $settings;
            $gallery->save();
        }

        // Get template options or default to grid options if template doesn't exist
        $templateOptions = $gallery->templateOptions[$gallery->template] 
            ?? $gallery->templateOptions['grid'] 
            ?? ['columns' => [3], 'default_columns' => 3];

        return view('galleries.preview', [
            'gallery' => $gallery,
            'media' => $gallery->media()->orderBy('sort_order')->get(),
            'settings' => $settings,
            'templateOptions' => $templateOptions,
        ]);
    }
} 