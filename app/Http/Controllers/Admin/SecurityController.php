<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Security;
use App\Models\Amc;
use League\Csv\Reader;
use League\Csv\Writer;

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
        $amcs = Amc::where('status', 1)->orderBy('name', 'asc')->get();
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
        //$security = Security::findOrFail($id);
        //$security->delete();
        //return redirect()->route('securities.index')->with('success', 'Security deleted successfully.');
    }

    public function uploadCSV(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $csv = Reader::createFromPath($file->getPathname());

        // Assuming CSV structure: isin,price
        $records = $csv->getRecords();

        foreach ($records as $record) {
            $isin = $record[0];
            $price = $record[1];
            $cash_component = $record[2];

            // Update securities table based on ISIN
            Security::where('isin', $isin)->update(['price' => $price, 'cash_component' => $cash_component]);
        }

        return redirect()->back()->with('success', 'Securities updated successfully as per ISIN.');
    }

    public function downloadCSV()
    {
        // Fetch required columns from securities table
        $securities = Security::select('amc_id', 'name', 'symbol', 'isin', 'basket_size', 'markup_percentage', 'price', 'cash_component', 'status')->get();

        // Create a new CSV writer instance
        $csv = Writer::createFromFileObject(new \SplTempFileObject());

        // Insert column headers
        $csv->insertOne(['AMC Id', 'Name', 'Symbol', 'ISIN', 'Basket Size', 'Markup Percentage', 'Price', 'Cash Component', 'Status']);

        // Insert data rows
        foreach ($securities as $security) {
            $csv->insertOne([
                $security->amc_id,
                $security->name,
                $security->symbol,
                $security->isin,
                $security->basket_size,
                $security->markup_percentage,
                $security->price,
                $security->cash_component,
                $security->status,
            ]);
        }

        // Set headers for CSV download
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="securities.csv"',
        ];

        // Create HTTP response with CSV content
        return response($csv->toString(), 200, $headers);
    }
}
