<?php

namespace Tests\Unit\Http\Filters;

use App\Http\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Mockery;

class QueryFilterTest extends QueryFilter
{
    public function name($value)
    {
        $this->builder->where('name', $value);
    }
}

it('applies filters to the builder', function () {
    $request = new Request(['name' => 'test']);
    $builder = Mockery::mock(Builder::class);
    $builder->shouldReceive('where')->with('name', 'test')->once();

    $filter = new QueryFilterTest($request);
    $filter->apply($builder);
});
