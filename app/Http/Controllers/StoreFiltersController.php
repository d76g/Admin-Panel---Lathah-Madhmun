<?php

namespace App\Http\Controllers;


class StoreFiltersController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        return view('store_filters.index');
    }


    public function edit($id)
    {
        
        return view('store_filters.edit')->with('id',$id);
    }

    public function create()
    {
        return view('store_filters.create');
    }    
}
