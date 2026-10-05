<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $events = Event::query()
            ->orderBy('start_date', 'desc')
            ->get();

        return view('dashboard', [
            'events' => $events,
        ]);
    }
}
