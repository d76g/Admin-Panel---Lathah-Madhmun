<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StoreController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    
	  public function index()
    {

        return view("stores.index");
    }

    public function vendors()
    {
        return view("vendors.index");
    }


    public function edit($id)
    {
    	    return view('stores.edit')->with('id',$id);
    }

    public function vendorEdit($id)
    {
    	    return view('vendors.edit')->with('id',$id);
    }

    public function vendorSubscriptionPlanHistory($id='')
    {
    	    return view('subscription_plans.history')->with('id',$id);
    }

    public function view($id)
    {
        return view('stores.view')->with('id',$id);
    }

    public function plan($id)
    {

        return view("stores.plan")->with('id',$id);
    }

    public function payout($id)
    {
        return view('stores.payout')->with('id',$id);
    }

    public function items($id)
    {
        return view('stores.items')->with('id',$id);
    }

    public function orders($id)
    {
        return view('stores.orders')->with('id',$id);
    }

    public function reviews($id)
    {
        return view('stores.reviews')->with('id',$id);
    }

    public function promos($id)
    {
        return view('stores.promos')->with('id',$id);
    }

    public function vendorCreate(){
        return view('vendors.create');
    }

    public function create(){
        return view('stores.create');
    }

    public function DocumentList($id){
        return view("vendors.document_list")->with('id',$id);
    }

    public function DocumentUpload($vendorId, $id)
    {
        return view("vendors.document_upload", compact('vendorId', 'id'));
    }
    public function currentSubscriberList($id)
    {
        return view("subscription_plans.current_subscriber", compact( 'id'));
    }
    public function vendorChat($id)
    {
        return view("vendors.chat", compact( 'id'));
    }
}
