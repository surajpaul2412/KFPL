<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Amc;
use App\Models\Pdf;
use Illuminate\Support\Facades\Storage;

class AmcController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $amcs = Amc::when($search, function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%');
        })->paginate(10);

        return view('admin.amcs.index', compact('amcs', 'search'));
    }

    public function create()
    {
        $pdfs = Pdf::orderBy('name')->get();
        return view('admin.amcs.create', compact('pdfs'));
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
        return view('admin.amcs.edit', compact('amc','pdfs'));
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
        ]);

        $amc = Amc::findOrFail($id);

        // Update amc attributes with validated data
        $amc->update($request->all());

        return redirect()->route('amcs.index')->with('success', 'AMC updated successfully.');
    }

    public function destroy($id)
    {
        $amc = Amc::findOrFail($id);
        $amc->delete();
        return redirect()->route('amcs.index')->with('success', 'AMC deleted successfully.');
    }
}
