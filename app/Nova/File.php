<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;

class FileItem extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\File';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'title';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'title',
        'file'
    ];

    /**
     * Overwrite the default ordering of the index page.
     *
     * @var array
     */
    public static $orderBy = [
        'title' => 'desc',
        'file' => 'desc',
    ];

    /**
     * Localize Name in side menu.
     *
     * @return string
     */
    static function label()
    {
        return __('resource.titles.file');
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            Text::make(__('fields.files.title'), 'title')
                ->sortable(),
            File::make(__('fields.files.file'), 'file')
                ->storeOriginalName('file_orig_name')
                ->storeSize('file_size')
                ->sortable(),
            Text::make(__('fields.files.page'), 'navID')
                ->readonly(),
            Text::make(__('fields.files.position'), 'position')
                ->readonly(),
        ];
    }

    public function fieldsForIndex(Request $request)
    {
        return [
            Text::make(__('fields.files.title'), function () {
                $extension = pathinfo(storage_path() . $this->file)['extension'];
                $url = $this->file;
                $title = $this->title;

                return view('backend.partials.index.file_title', compact('extension', 'url', 'title'))->render();
            })->asHtml(),
            File::make(__('fields.files.file'), 'file')
                ->sortable(),
            ID::make(__('fields.files.page'), 'navID'),
            ID::make(__('fields.files.position'), 'position'),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
