<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Page extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Page';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'seitentitel';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'seitentitel',
        'inhalt',
    ];

    /**
     * Overwrite the default ordering of the index page.
     *
     * @var array
     */
    public static $orderBy = [
        'seitentitel' => 'asc',
    ];

    /**
     * Localize Name in side menu.
     *
     * @return string
     */
    static function label()
    {
        return __('resource.titles.pages');
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
            Text::make(__('fields.pages.title'), 'seitentitel')
                ->sortable(),
            Trix::make(__('fields.pages.content'), 'inhalt'),
        ];
    }

    /**
     * Override fields for index view.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function fieldsForIndex(NovaRequest $request)
    {
        return [
            Text::make(__('fields.pages.title'), 'seitentitel')
                ->sortable(),
            Text::make(__('fields.pages.content'), 'inhalt')
                ->asHtml(),
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
