<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeliverymanController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($id = '')
    {
        return view('deliveryman.index')->with('id', $id);
    }
    public function create()
    {
        return view("deliveryman.create");
    }
    public function edit($id)
    {
        return view("deliveryman.edit")->with('id', $id);
    }
}
