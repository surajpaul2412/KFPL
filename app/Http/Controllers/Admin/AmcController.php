<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Amc;

class AmcController extends Controller
{
    public function index(Request $request)
    {
        // Retrieve the search query from the request
        $search = $request->input('search');

        // Query to fetch AMC records with optional search
        $amcs = Amc::when($search, function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%');
        })->paginate(10); // You can adjust the pagination value as needed

        return view('admin.amcs.index', compact('amcs', 'search')); // Pass 'amcs' and 'search' as variables to the view
    }

    public function create()
    {
        return view('admin.amcs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'pdf' => 'nullable|mimes:pdf',
            'status' => 'nullable|in:0,1',
        ]);

        // Handle file upload if necessary
        if ($request->hasFile('pdf')) {
            $pdfPath = $request->file('pdf')->store('pdfs');
        } else {
            $pdfPath = null;
        }

        Amc::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'pdf' => $pdfPath,
            'status' => $request->input('status', 1),
        ]);

        return redirect()->route('admin.amcs.index')->with('success', 'AMC created successfully.');
    }

    public function edit($id)
    {
        $amc = Amc::find($id);

        return view('admin.amcs.edit', compact('amc'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'pdf' => 'nullable|mimes:pdf',
            'status' => 'nullable|in:0,1',
        ]);

        $amc = Amc::find($id);

        // Handle file upload if necessary
        if ($request->hasFile('pdf')) {
            // Delete the existing file
            if ($amc->pdf) {
                Storage::delete($amc->pdf);
            }

            // Upload the new file
            $pdfPath = $request->file('pdf')->store('pdfs');
            $amc->pdf = $pdfPath;
        }

        $amc->name = $request->input('name');
        $amc->email = $request->input('email');
        $amc->status = $request->input('status', 1);
        $amc->save();

        return redirect()->route('admin.amcs.index')->with('success', 'AMC updated successfully.');
    }

    public function destroy($id)
    {
        $amc = Amc::find($id);

        // Delete the associated file
        if ($amc->pdf) {
            Storage::delete($amc->pdf);
        }

        $amc->delete();

        return redirect()->route('admin.amcs.index')->with('success', 'AMC deleted successfully.');
    }
}
