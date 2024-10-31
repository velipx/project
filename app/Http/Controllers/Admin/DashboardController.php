<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(): \Inertia\Response
    {
//        $users = User::all();


        return Inertia::render('Admin/Dashboard/Index', [
//            'users' => $users,
//            'permissions' => Inertia::defer(fn () => Permission::all()),
        ]);
    }
}
