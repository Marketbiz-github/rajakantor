<?php
namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Count total products
        $totalProducts = DB::table('products')->where('status', 1)->count();

        // Count total categories
        $totalCategories = DB::table('categories')->where('status', 1)->count();

        return view('admin.dashboard', compact('totalProducts', 'totalCategories'));
    }
}