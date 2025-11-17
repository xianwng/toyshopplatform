<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function index() 
    {
        return view('frontend.index');
    }

    public function product() 
    {
        return view('frontend.product');
    }

    public function auction() 
    {
        return view('frontend.auction');
    }

    public function trading() 
    {
        return view('frontend.trading');
    }

    public function order() 
    {
        return view('frontend.order');
    }

    public function delivery() 
    {
        return view('frontend.delivery');
    }

        public function reports() 
    {
        return view('frontend.reports');
    }

      public function customer() 
    {
        return view('frontend.customer');
    }
    
}
