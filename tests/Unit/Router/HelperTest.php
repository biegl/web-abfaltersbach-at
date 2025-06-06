<?php

namespace Tests\Unit\Router;

use App\Router\Helper;

it('normalizes urls', function ($url, $expected) {
    expect(Helper::normalizeUrl($url))->toBe($expected);
})->with([
    ['/', '/'],
    ['/foo', '/foo'],
    ['foo', '/foo'],
    ['foo/', '/foo'],
    ['/foo/', '/foo'],
    ['', '/'],
]);

it('segmentizes urls', function ($url, $expected) {
    expect(Helper::segmentizeUrl($url))->toBe($expected);
})->with([
    ['/', []],
    ['/foo', ['foo']],
    ['foo', ['foo']],
    ['foo/bar', ['foo', 'bar']],
    ['/foo/bar', ['foo', 'bar']],
]);

it('rebuilds urls', function ($segments, $expected) {
    expect(Helper::rebuildUrl($segments))->toBe($expected);
})->with([
    [[], '/'],
    [['foo'], '/foo'],
    [['foo', 'bar'], '/foo/bar'],
]);

it('parses values', function () {
    $obj = new \stdClass();
    $obj->id = 1;
    $obj->name = 'Joe';

    $str = '/some/link/:id/:name';

    expect(Helper::parseValues($obj, ['id', 'name'], $str))->toBe('/some/link/1/Joe');
});

it('replaces parameters', function () {
    $obj = new \stdClass();
    $obj->id = 1;
    $obj->name = 'Joe';

    $str = '/some/link/:id/:name';

    expect(Helper::replaceParameters($obj, $str))->toBe('/some/link/1/Joe');
});

it('checks if a segment is a wildcard', function ($segment, $expected) {
    expect(Helper::segmentIsWildcard($segment))->toBe($expected);
})->with([
    [':foo*', true],
    [':foo', false],
    ['foo', false],
]);

it('checks if a segment is optional', function ($segment, $expected) {
    expect(Helper::segmentIsOptional($segment))->toBe($expected);
})->with([
    [':foo?', true],
    [':foo?|regexp', true],
    [':foo*?', true],
    [':foo', false],
]);

it('gets parameter name', function ($segment, $expected) {
    expect(Helper::getParameterName($segment))->toBe($expected);
})->with([
    [':foo', 'foo'],
    [':foo?', 'foo'],
    [':foo?|regexp', 'foo'],
    [':foo*?', 'foo'],
    [':foo*|regexp', 'foo'],
]);

it('gets segment regular expression', function ($segment, $expected) {
    expect(Helper::getSegmentRegExp($segment))->toBe($expected);
})->with([
    [':foo|regexp', '/regexp/'],
    [':foo', false],
]);

it('gets segment default value', function ($segment, $expected) {
    expect(Helper::getSegmentDefaultValue($segment))->toBe($expected);
})->with([
    [':foo?bar', 'bar'],
    [':foo?bar|regexp', 'bar'],
    [':foo', false],
]); 