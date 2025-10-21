<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityController extends Controller
{
    //
    public function index()
    {
        $activities = Activity::with('causer')
            ->where('log_name', 'create-va')
            ->latest()
            ->get();

        return view('activities.index', compact('activities'));
    }

    public function authLog()
    {
        $activities = Activity::with('causer')
            ->where('log_name', 'login')
            ->orWhere('log_name', 'logout')
            ->latest()
            ->get();

        return view('activities.authLog', compact('activities'));
    }
}
