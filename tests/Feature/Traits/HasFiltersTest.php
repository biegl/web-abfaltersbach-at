<?php

use App\Http\Filters\QueryFilter;
use App\Traits\HasFilters;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

// 1. Dummy Model using the trait
class DummyModelForFilterTrait extends Model
{
    use HasFilters;

    protected $table = 'dummy_models';

    protected $guarded = [];
}

// 2. Dummy QueryFilter class
class DummyFilter extends QueryFilter
{
    public function name($value)
    {
        return $this->builder->where('name', $value);
    }

    public function status($value)
    {
        return $this->builder->where('status', $value);
    }
}

// 3. Setup and Test
beforeEach(function () {
    // Create a temporary table for our dummy model
    Schema::create('dummy_models', function ($table) {
        $table->id();
        $table->string('name');
        $table->string('status');
        $table->timestamps();
    });

    // Populate the table
    DummyModelForFilterTrait::create(['name' => 'John Doe', 'status' => 'active']);
    DummyModelForFilterTrait::create(['name' => 'Jane Doe', 'status' => 'active']);
    DummyModelForFilterTrait::create(['name' => 'Peter Pan', 'status' => 'inactive']);
});

afterEach(function () {
    Schema::dropIfExists('dummy_models');
});

test('it can be filtered by a query filter class', function () {
    // Simulate a request with filter parameters
    $request = new Request([
        'name' => 'John Doe',
        'status' => 'active',
    ]);
    $filter = new DummyFilter($request);

    $results = DummyModelForFilterTrait::filter($filter)->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->name)->toBe('John Doe');
});

test('it returns multiple records when filter matches them', function () {
    $request = new Request(['status' => 'active']);
    $filter = new DummyFilter($request);

    $results = DummyModelForFilterTrait::filter($filter)->get();

    expect($results)->toHaveCount(2);
});

test('it returns all records when no filter is applied', function () {
    $request = new Request; // Empty request
    $filter = new DummyFilter($request);

    $results = DummyModelForFilterTrait::filter($filter)->get();

    expect($results)->toHaveCount(3);
});
