<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Amc;
use App\Models\Pdf;
use Illuminate\Support\Facades\Storage;
use App\Models\Emailtemplate;
use App\Models\Senderemail;

class AmcController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $amcs = Amc::when($search, function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%');
        })
        ->paginate(10);

        return view('admin.amcs.index', compact('amcs', 'search'));
    }

    public function create()
    {
        $pdfs = Pdf::orderBy('name')->get();
        $senderemails = Senderemail::where("status", 1)->get();
		$emailtemplates = Emailtemplate::where('status',1)->get();
        return view('admin.amcs.create', compact('pdfs', 'emailtemplates', 'senderemails'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'expense_percentage' => 'required|numeric',
            'pdf_id' => 'nullable|exists:pdfs,id', // Validate that the selected PDF exists in the 'pdfs' table
            'status' => 'nullable|in:0,1',
        ]);

        Amc::create($request->all());

        return redirect()->route('amcs.index')->with('success', 'AMC created successfully.');
    }

    public function edit($id)
    {
        $amc = Amc::findOrFail($id);
        $pdfs = Pdf::all();
		$emailtemplates = Emailtemplate::where('status',1)->get();
        $senderemails = Senderemail::where("status", 1)->get();
        return view('admin.amcs.edit', compact('amc','pdfs','emailtemplates', 'senderemails'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'expense_percentage' => 'required|numeric',
            'pdf_id' => 'nullable|exists:pdfs,id', // Validate that the selected PDF exists in the 'pdfs' table
            'status' => 'nullable|in:0,1',
			'amc_pdf' => 'required|between:0,1',
			'generate_form_pdf' => 'required|between:0,1',
			
        ]);

        $amc = Amc::findOrFail($id);

        // Update amc attributes with validated data
        $amc->update($request->all());

        // IF AMC is being Disabled, DISABLE all Securities under it 
        if( $request->status == 0 )
        {
            foreach ($amc->securities as $security) {
                $security->status = 0;
                $security->save();
            }
        }

        return redirect()->route('amcs.index')->with('success', 'AMC updated successfully.');
    }

    public function destroy($id)
    {
        //$amc = Amc::findOrFail($id);
        //$amc->is_deleted = 1;
        //$amc->save();
        //return back()->with('success', 'AMC Deleted successfully.');
    }
}
