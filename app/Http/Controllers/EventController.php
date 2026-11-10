<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function index(Request $request)
    {
        DB::enableQueryLog();
        $mode = $request->query('mode', 'fixed');   // naive or fixed

        if ($request->query('strict') === '0') {
            Model::preventLazyLoading(false);       // lets you count the naive queries
        }

        $query = Event::query()->orderBy('id')->limit(50);

        if ($mode === 'fixed') {
            $query->with('venue')->withCount('participants');
        }
        if ($request->filled('venue')) {
            $query->atVenue((int) $request->query('venue'));
        }
        if ($request->filled('upcoming')) {
            $query->upcoming();
        }
        if ($request->filled('theme')) {
            $request->query('filter') === 'contains'
                ? $query->withThemeContains($request->query('theme'))
                : $query->withTheme($request->query('theme'));
        }

        $sql = $query->toSql();
        $events = $query->get();

        return view('events.index', compact('events', 'mode', 'sql'));
    }

    public function search(Request $request)
    {
        $term = (string) $request->query('q', '');

        $query = Event::query()->with('venue')->searchDescription($term)->limit(50);
        $sql = $query->toSql();
        $events = $query->get();

        return view('events.search', compact('events', 'term', 'sql'));
    }
}