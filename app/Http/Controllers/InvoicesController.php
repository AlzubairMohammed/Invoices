<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\sections;
use App\invoices;
use App\invoices_details;
use App\User;
use App\invoice_attachments;
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
        // $validatedData = $request->validate([
        //     'invoice_number' => 'unique:invoices',
        // ],[
        //     'invoice_number.unique' =>'رقم الفاتورة مسجل مسبقا',
        // ]);
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

        if ($request->hasFile('pic')) {

            $invoice_id = Invoices::latest()->first()->id;
            $image = $request->file('pic');
            $file_name = $image->getClientOriginalName();
            $invoice_number = $request->invoice_number;

            $attachments = new invoice_attachments();
            $attachments->file_name = $file_name;
            $attachments->invoice_number = $invoice_number;
            $attachments->created_by = Auth::user()->name;
            $attachments->invoice_id = $invoice_id;
            $attachments->save();

            // move pic
            $imageName = $request->pic->getClientOriginalName();
            $request->pic->move(public_path('Attachments/' . $invoice_number), $imageName);
        }


           // $user = User::first();
           // Notification::send($user, new AddInvoice($invoice_id));

        // $user = User::get();
        // $invoices = invoices::latest()->first();
        // Notification::send($user, new \App\Notifications\Add_invoice_new($invoices));








        session()->flash('add', 'تم اضافة الفاتورة بنجاح');
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $invoices = invoices::where('id', $id)->first();
        return view('invoices.status_update', compact('invoices'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $invoices = invoices::where('id', $id)->first();
        $sections = sections::all();
        return view('invoices.edit_invoice', compact('sections', 'invoices'));
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
        $invoices = invoices::findOrFail($request->invoice_id);
        $invoices->update([
            'invoice_number' => $request->invoice_number,
            'invoices_date' => $request->invoices_date,
            'due_date' => $request->due_date,
            'product' => $request->product,
            'section_id' => $request->section,
            'amount_collection' => $request->amount_collection,
            'amount_commission' => $request->amount_commission,
            'discount' => $request->discount,
            'value_vat' => $request->value_vat,
            'rate_vat' => $request->rate_vat,
            'total' => $request->total,
            'note' => $request->note,
        ]);

        session()->flash('edit', 'تم تعديل الفاتورة بنجاح');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $id = $request->invoice_id;
        $invoices = invoices::where('id', $id)->first();
        $details = invoice_attachments::where('invoice_id', $id)->first();

        $id_page =$request->id_page;


        if (!$id_page==2) {

        if (!empty($details->invoice_number)) {

            Storage::disk('public_uploads')->deleteDirectory($details->invoice_number);
        }

        $invoices->forceDelete();
        session()->flash('delete_invoice');
        return redirect('/invoices');

        }

        else {

            $invoices->delete();
            session()->flash('archive_invoice');
            return redirect('/archive');
        }
    }

    public function getproducts($id)
    {
        $products = DB::table("products")->where("section_id", $id)->pluck("product_name", "id");

        return json_encode($products);
    }

    public function status_update($id, Request $request) {
        $invoices = invoices::findOrFail($id);

            $invoices->update([
                'status_value' => ($request->status === 'مدفوعة')? 1 : 3,
                'status' => $request->status,
                'payment_date' => $request->payment_date,
            ]);

            invoices_details::create([
                'invoice_id' => $request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'section' => $request->section,
                'status' => $request->status,
                'status_value' => 1,
                'note' => $request->note,
                'payment_date' => $request->payment_date,
                'user' => (Auth::user()->name),
            ]);


        // else {
        //     $invoices->update([
        //         'value_status' => 3,
        //         'status' => $request->status,
        //         'payment_date' => $request->payment_date,
        //     ]);

        //     invoices_Details::create([
        //         'invoice_id' => $request->invoice_id,
        //         'invoice_number' => $request->invoice_number,
        //         'product' => $request->product,
        //         'section' => $request->section,
        //         'status' => $request->status,
        //         'value_status' => 1,
        //         'note' => $request->note,
        //         'payment_date' => $request->payment_date,
        //         'user' => (Auth::user()->name),
        //     ]);
        // }
        session()->flash('Status_Update');
        return redirect('/invoices');

    }

    public function invoicesPaid() {
        $invoices = Invoices::where('status_value', 1)->get();
        return view('invoices.invoices_paid',compact('invoices'));
    }

    public function invoicesUnPaid() {
        $invoices = Invoices::where('status_value', 2)->get();
        return view('invoices.invoices_unpaid',compact('invoices'));
    }

    public function invoicesPartial() {
        $invoices = Invoices::where('status_value', 3)->get();
        return view('invoices.invoices_partial',compact('invoices'));
    }
}
