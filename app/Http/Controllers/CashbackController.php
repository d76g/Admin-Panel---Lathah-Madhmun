<?php

namespace App\Http\Controllers;

class CashbackController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view("cashback.index");
    }

    public function edit($id)
    {
        return view('cashback.edit')->with('id', $id);
    }

    public function create()
    {
        return view('cashback.create');
    }
    public function redeemList($id)
    {
        return view('cashback.redeem_list')->with('id', $id);
    }
}
