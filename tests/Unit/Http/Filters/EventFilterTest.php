<?php

namespace Tests\Unit\Http\Filters;

use App\Http\Filters\EventFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Mockery;

it('filters by event id', function () {
    $request = new Request(['eventID' => 1]);
    $builder = Mockery::mock(Builder::class);
    $builder->shouldReceive('where')->with('ID', 1)->once();

    $filter = new EventFilter($request);
    $filter->apply($builder);
});

it('does not filter by event id if it is not present', function () {
    $request = new Request();
    $builder = Mockery::mock(Builder::class);
    $builder->shouldNotReceive('where');

    $filter = new EventFilter($request);
    $filter->apply($builder);
});

it('does not filter by event id if it is null', function () {
    $request = new Request(['eventID' => null]);
    $builder = Mockery::mock(Builder::class);
    $builder->shouldNotReceive('where');

    $filter = new EventFilter($request);
    $filter->apply($builder);
}); 