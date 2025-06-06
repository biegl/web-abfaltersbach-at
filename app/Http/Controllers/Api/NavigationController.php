<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Navigation;

class NavigationController extends Controller
{
    private $itemsPerPage = 25;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $showAll = request()->query('showAll');

        if ($showAll) {
            return Navigation::allTopLevel()
                ->orderBy('position')
                ->get();
        }

        return Navigation::allTopLevel()
            ->visible()
            ->orderBy('position')
            ->paginate($this->itemsPerPage);
    }
}
