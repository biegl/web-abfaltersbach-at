<?php

use App\Support\Str;

test('ordinal converts numbers to ordinal strings', function ($number, $expected) {
    expect(Str::ordinal($number))->toBe($expected);
})->with([
    [1, '1st'],
    [2, '2nd'],
    [3, '3rd'],
    [4, '4th'],
    [10, '10th'],
    [11, '11th'],
    [12, '12th'],
    [13, '13th'],
    [21, '21st'],
    [22, '22nd'],
    [101, '101st'],
    [111, '111th'],
]);

test('normalizeEol converts different line endings to crlf', function () {
    $string = "line1\nline2\r\nline3\rline4";
    $expected = "line1\r\nline2\r\nline3\r\nline4";
    expect(Str::normalizeEol($string))->toBe($expected);
});

test('normalizeClassName adds leading slash and works with objects', function () {
    // Test with a string
    expect(Str::normalizeClassName('App\MyClass'))->toBe('\App\MyClass');

    // Test with a string that already has a slash
    expect(Str::normalizeClassName('\App\MyClass'))->toBe('\App\MyClass');

    // Test with an object
    $object = new \stdClass();
    expect(Str::normalizeClassName($object))->toBe('\stdClass');
});

test('getClassId converts class names and objects to a lowercase underscore id', function () {
    // Test with a string
    expect(Str::getClassId('App\Models\User'))->toBe('app_models_user');

    // Test with a string that has a leading slash
    expect(Str::getClassId('\App\Models\User'))->toBe('app_models_user');

    // Test with an object
    $object = new \App\Models\User();
    expect(Str::getClassId($object))->toBe('app_models_user');
});

test('getClassNamespace extracts the namespace from a class name or object', function () {
    // Test with a string
    expect(Str::getClassNamespace('App\Models\User'))->toBe('\App\Models');

    // Test with a string that has a leading slash
    expect(Str::getClassNamespace('\App\Models\User'))->toBe('\App\Models');

    // Test with an object
    $object = new \App\Models\User();
    expect(Str::getClassNamespace($object))->toBe('\App\Models');

    // Test with a class in the root namespace
    expect(Str::getClassNamespace('stdClass'))->toBe('');
});

test('getPrecedingSymbols counts consecutive symbols at the start of a string', function ($string, $symbol, $expected) {
    expect(Str::getPrecedingSymbols($string, $symbol))->toBe($expected);
})->with([
    ['> > > Hello', '>', 1], // It should count the very first > and then stop.
    ['>>> Hello', '>', 3],
    ['  Indented', ' ', 2],
    ['NoSymbols', '>', 0],
    ['....Start', '.', 4],
    ['Hello', 'H', 1],
]);
