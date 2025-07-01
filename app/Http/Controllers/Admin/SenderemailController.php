<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Amc;
use Illuminate\Support\Facades\Storage;
use App\Models\Senderemail;

class SenderemailController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $senderemails = Senderemail::when($search, function ($query) use ($search) {
            $query->where('driver', 'like', '%' . $search . '%')
                ->orWhere('host', 'like', '%' . $search . '%')
                ->orWhere('port', 'like', '%' . $search . '%')
                ->orWhere('username', 'like', '%' . $search . '%')
                ->orWhere('from_address', 'like', '%' . $search . '%')
                ->orWhere('from_name', 'like', '%' . $search . '%')
                ->orWhere('reply_to_address', 'like', '%' . $search . '%')
                ;
        })
        ->paginate(10);

        return view('admin.senderemails.index', compact('senderemails', 'search'));
    }

    public function create()
    {
        return view('admin.senderemails.create', []);
    }

    public function store(Request $request)
    {    
        $validatedData = $request->validate([
            'host' => 'required',
            'port' => 'required',
            'username' => 'required',
            'password' => 'required',
            'encryption' => 'required',
            'from_address' => 'required',
            'from_name' => 'required',
            'status' => 'in:0,1',
        ]);

        $validatedData['driver'] = 'smtp';
        $validatedData['reply_to_address'] = $validatedData['from_address'];

        Senderemail::create( $validatedData );

        return redirect()->route('senderemail.index')->with('success', 'Email Sender created successfully.');
    }

    public function edit($id)
    {
        $emailsender = Senderemail::findOrFail($id);
        return view('admin.senderemails.edit', compact('emailsender'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'host' => 'required',
            'port' => 'required',
            'username' => 'required',
            'password' => 'required',
            'encryption' => 'required',
            'from_address' => 'required',
            'from_name' => 'required',
            'status' => 'in:0,1',
        ]);

        $validatedData['reply_to_address'] = $validatedData['from_address'];

        $emailsender = Senderemail::findOrFail($id);

        // Update amc attributes with validated data
        $emailsender->update( $validatedData );

        return redirect()->route('senderemail.index')->with('success', 'Email Sender updated successfully.');
    }

    public function destroy($id)
    {
        $emailsender = Senderemail::findOrFail($id);
        $emailsender->delete();
        return back()->with('success', 'Email Sender deleted successfully.');
    }
}
