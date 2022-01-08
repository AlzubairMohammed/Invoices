<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use App\sections;
use App\invoices;
use Illuminate\Http\Request;

class InvoicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoices = invoices::all();
        return view('invoices.invoices',compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $sections = sections::all();
        return view('invoices.add_invoice', compact('sections'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'invoice_number' => 'unique:invoices',
        ],[
            'invoice_number.unique' =>'رقم الفاتورة مسجل مسبقا',
        ]);
        invoices::create([
            'invoice_number'=>$request->invoice_number,
            'invoices_date'=>$request->invoices_date,
            'due_date'=>$request->due_date,
            'product'=>$request->product,
            'section_id'=>$request->section,
            'amount_collection'=>$request->amount_collection,
            'amount_commission'=>$request->amount_commission,
            'discount'=>$request->discount,
            'rate_vat'=>$request->rate_vat,
            'value_vat'=>$request->value_vat,
            'total'=>$request->total,
            'status'=>'غير مدفوع',
            'status_value'=>2,
            'note'=>$request->note,
            'invoice_number'=>$request->invoice_number,
        ]);
        session()->flash('add', 'تم اضافة الفاتورة بنجاح ');
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function show(invoices $invoices)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function edit(invoices $invoices)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, invoices $invoices)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function destroy(invoices $invoices)
    {
        //
    }

    public function getproducts($id)
    {
        $products = DB::table("products")->where("section_id", $id)->pluck("product_name", "id");

        return json_encode($products);
    }
}
