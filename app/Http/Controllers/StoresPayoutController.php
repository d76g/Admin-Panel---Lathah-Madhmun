<?php

namespace App\Http\Controllers;


class StoresPayoutController extends Controller
{  

   public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($id='')
    {

       return view("stores_payouts.index")->with('id',$id);
    }

    public function create($id='')
    {
        
       return view("stores_payouts.create")->with('id',$id);
    }

}
