<?php

namespace App\Http\Controllers\Api;

use App\Customer;
use App\Http\Resources\CustomerCollection;
use App\Http\Resources\CustomerResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new CustomerCollection(Customer::orderBy('id','DESC')->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'name' => 'required',
            'email' => 'required|email|unique:customers',
            'phone' => 'required|numeric',
            'addres' => 'required',
            'tobal' => 'required|numeric',
        ]);
        $customer = new Customer();
        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->addres = $request->addres;
        $customer->tobal = $request->tobal;
        $customer->save();
        return new CustomerResource($customer);
    }

    public function search($field,$query)
    {
        return new CustomerCollection(Customer::where($field,'LIKE',"%$query%")->latest()->paginate(10));
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return new CustomerResource(Customer::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'name' => 'required',
            'email' => 'required|email|unique:customers,email,'.$id,
            'phone' => 'required|numeric',
            'addres' => 'required',
            'tobal' => 'required|numeric',
        ]);
        $customer = Customer::findOrFail($id);
        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->addres = $request->addres;
        $customer->tobal = $request->tobal;
        $customer->save();
        return new CustomerResource($customer);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        return new CustomerResource($customer);
    }
}
