<?php

namespace App\Http\Controllers;
use App\sections;
use App\invoices;
use Illuminate\Http\Request;

class InvoicesReportController extends Controller
{
    public function index(){
        $sections = sections::all();
        return view('reports.invoicesReport',compact('sections'));

    }

    public function invoicesSearch(Request $request) {

        $rdio = $request->rdio;


 // في حالة البحث بنوع الفاتورة

    if ($rdio == 1) {


 // في حالة عدم تحديد تاريخ
        if ($request->type && $request->start_at =='' && $request->end_at =='') {

           $invoices = invoices::select('*')->where('Status','=',$request->type)->get();
           $type = $request->type;
           return view('reports.invoices_report',compact('type'))->withDetails($invoices);
        }

        // في حالة تحديد تاريخ استحقاق
        else {

          $start_at = date($request->start_at);
          $end_at = date($request->end_at);
          $type = $request->type;

          $invoices = invoices::whereBetween('invoices_date',[$start_at,$end_at])->where('status','=',$request->type)->get();
          return view('reports.invoicesReport',compact('type','start_at','end_at'))->withDetails($invoices);

        }



    }

//====================================================================

// في البحث برقم الفاتورة
    else {

        $invoices = invoices::select('*')->where('invoice_number','=',$request->invoice_number)->get();
        return view('reports.invoicesReport')->withDetails($invoices);

    }
    }
}
