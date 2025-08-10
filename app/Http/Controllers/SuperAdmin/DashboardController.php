<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Desa;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalDesa = Desa::count();
            $totalAdminDesa = User::where('user_type', 'admin_desa')->count();
            $totalActiveSubscriptions = Desa::where('subscription_status', 'active')->count();
            $totalInactiveSubscriptions = Desa::where('subscription_status', 'inactive')->count();
            
            $usersByType = User::select('user_type', DB::raw('count(*) as total'))
                               ->groupBy('user_type')
                               ->get()
                               ->pluck('total', 'user_type')
                               ->toArray();

            return view('superadmin.dashboard', compact(
                'totalDesa', 'totalAdminDesa', 'totalActiveSubscriptions', 'totalInactiveSubscriptions', 'usersByType'
            ));
    }
}