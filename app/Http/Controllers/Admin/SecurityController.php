<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Security;
use App\Models\Amc;

class SecurityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $securities = Security::with('amc')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('symbol', 'like', '%' . $search . '%')
                    ->orWhere('isin', 'like', '%' . $search . '%');
            })
            ->paginate(10); // You can adjust the pagination value as needed

        return view('admin.securities.index', compact('securities', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $amcs = Amc::all();
        return view('admin.securities.create', ['amcs' => $amcs]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'amc_id' => 'required|exists:amcs,id',
            'name' => 'required',
            'symbol' => 'nullable',
            'isin' => 'nullable',
            'basket_size' => 'required|integer',
            'markup_percentage' => 'required|numeric',
            'price' => 'required|numeric',
            'status' => 'nullable|in:0,1',
        ]);

        Security::create($request->all());

        return redirect()->route('securities.index')->with('success', 'Security created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $security = Security::findOrFail($id);
        $amcs = Amc::all();
        return view('admin.securities.edit', compact('security', 'amcs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $security = Security::findOrFail($id);

        $request->validate([
            'amc_id' => 'required|exists:amcs,id',
            'name' => 'required',
            'symbol' => 'nullable',
            'isin' => 'nullable',
            'basket_size' => 'required|integer',
            'markup_percentage' => 'required|numeric',
            'price' => 'required|numeric',
            'status' => 'nullable|in:0,1',
        ]);

        $security->update($request->all());

        return redirect()->route('securities.index')->with('success', 'Security updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $security = Security::findOrFail($id);
        $security->delete();

        return redirect()->route('securities.index')->with('success', 'Security deleted successfully.');
    }
}
