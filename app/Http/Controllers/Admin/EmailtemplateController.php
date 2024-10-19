<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Security;
use App\Models\Amc;
use App\Models\Emailtemplate;
use League\Csv\Reader;
use League\Csv\Writer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmailtemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        
		DB::enableQueryLog();
		
		$etquery = Emailtemplate::query();
		
		if( $search != '')
		{
            $etquery->where('name', 'like', '%' . $search . '%')  
				    ->orWhere('template', 'like', '%' . $search . '%')
			;				
		}
        
		$emailtemplates = $etquery->paginate(10); // You can adjust the pagination value as needed
		
		$sql = DB::getQueryLog();

        return view('admin.emailtemplates.index', compact('emailtemplates', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $amcs = Amc::all();
        return view('admin.emailtemplates.create', ['amcs' => $amcs]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'amc_id' => 'required|exists:amcs,id',
            'name' => 'required',
            'type' => 'required|in:1,2',
            'status' => 'required|in:0,1',
        ]);

        Emailtemplate::create($request->all());

        return redirect()->route('admin.emailtemplates.index')->with('success', 'AMC Email Template created successfully.');
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
        $emailtemplate = Emailtemplate::findOrFail($id);
        $amcs = Amc::all();
        return view('admin.emailtemplates.edit', compact('emailtemplate', 'amcs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $emailtemplate = Emailtemplate::findOrFail($id);

        $request->validate([
		    'amc_id' => 'required|exists:amcs,id',
            'name' => 'required',
            'type' => 'required|in:1,2',
            'status' => 'required|in:0,1',
        ]);

        $emailtemplate->update($request->all());

        return redirect()->route('admin.emailtemplates.index')->with('success', 'AMC Email Template updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $emailtemplate = Emailtemplate::findOrFail($id);
        $emailtemplate->delete();

        return redirect()->route('admin.emailtemplates.index')->with('success', 'AMC Email Template deleted successfully.');
    }

}
