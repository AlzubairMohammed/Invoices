<?php

namespace App\Http\Controllers;
use App\sections;
use App\invoices;
use Illuminate\Http\Request;

class CustomersReportController extends Controller
{
    public function customersReport() {
        $sections = sections::all();
        return view('reports.customersReport',compact('sections'));
    }

    public function customersSearch(Request $request) {

        if ($request->section && $request->product && $request->start_at =='' && $request->end_at=='') {


            $invoices = invoices::select('*')->where('section_id','=',$request->Section)->where('product','=',$request->product)->get();
            $sections = sections::all();
             return view('reports.customersReport',compact('sections'))->withDetails($invoices);


           }


        // في حالة البحث بتاريخ

           else {

             $start_at = date($request->start_at);
             $end_at = date($request->end_at);

            $invoices = invoices::whereBetween('invoices_date',[$start_at,$end_at])->where('section_id','=',$request->section)->where('product','=',$request->product)->get();
             $sections = sections::all();
             return view('reports.customersReport',compact('sections'))->withDetails($invoices);


           }
    }
}
