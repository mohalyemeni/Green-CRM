<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Customer;
use App\Models\Opportunity;
use App\Models\Service;
use App\Models\Quotation;

class BackendController extends Controller
{
    public function login(){
        return view('backend.login');
    }

    public function forgetPassword(){
        return view('backend.forget-password');
    }

    public function index(){
        $stats = [
            'leads' => Lead::count(),
            'customers' => Customer::count(),
            'opportunities' => Opportunity::count(),
            'services' => Service::count(),
            'quotations' => Quotation::count(),
            'won_opportunities' => Opportunity::whereHas('stage', function($q) {
                $q->where('is_won', true);
            })->count(),
            'lost_opportunities' => Opportunity::whereHas('stage', function($q) {
                $q->where('is_lost', true);
            })->count(),
            'total_revenue' => Opportunity::whereHas('stage', function($q) {
                $q->where('is_won', true);
            })->sum('expected_revenue'),
        ];

        return view('backend.home.index', compact('stats'));
    }
}
