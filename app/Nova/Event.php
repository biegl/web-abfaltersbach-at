<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Http\Requests\NovaRequest;

class Event extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Event';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'text';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'date',
        'text',
        'filepath',
    ];

    /**
     * Overwrite the default ordering of the index page.
     *
     * @var array
     */
    public static $orderBy = [
        'date' => 'desc',
        'ID' => 'desc',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            Date::make(__('fields.event.date'), 'date')
                ->format('YYYY-MM-DD')
                ->sortable(),
            Trix::make(__('fields.event.text'), 'text')
                ->alwaysShow(),
            Text::make(__('fields.event.filepath'), 'filepath'),
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
            Date::make(__('fields.event.date'), 'date')
                ->format('YYYY-MM-DD')
                ->sortable(),
            Text::make(__('fields.event.text'), 'text')
                ->asHtml(),
            Text::make(__('fields.event.filepath'), 'filepath'),
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
