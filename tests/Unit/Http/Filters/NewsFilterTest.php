<?php

namespace Tests\Unit\Http\Filters;

use App\Http\Filters\NewsFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Mockery;

it('filters by news id', function () {
    $request = new Request(['newsID' => 1]);
    $builder = Mockery::mock(Builder::class);
    $builder->shouldReceive('where')->with('ID', 1)->once();

    $filter = new NewsFilter($request);
    $filter->apply($builder);
});

it('does not filter by news id if it is not present', function () {
    $request = new Request;
    $builder = Mockery::mock(Builder::class);
    $builder->shouldNotReceive('where');

    $filter = new NewsFilter($request);
    $filter->apply($builder);
});

it('does not filter by news id if it is null', function () {
    $request = new Request(['newsID' => null]);
    $builder = Mockery::mock(Builder::class);
    $builder->shouldNotReceive('where');

    $filter = new NewsFilter($request);
    $filter->apply($builder);
});
