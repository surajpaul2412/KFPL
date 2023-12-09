<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use Exception;
use Validator;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('isAdmin');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $role_id = $request['role_id'];

        $employees = User::whereHas('roles', function($query) use ($role_id) {
            $query->where('roles.id', '<>', 1);
            if( $role_id !='' )
            {
              $query->where('roles.id', $role_id);
            }
        })
        ->orderBy('users.name')
        ->get();

        $roles = Role::where('id', '<>', 1)->get();

        return view('admin.employee.index', compact('employees', 'roles', 'role_id'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::where('id', '<>', 1)->get();

        return view('admin.employee.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        try
        {
            // Validate Request
            $rules = [
        		  'name'      => 'required',
        		  'email'     => 'required|email|Unique:users',
        		  'role_id'   => 'required|array|min:1',
        		];

            $validator = Validator::make($request->all(),$rules);

            if($validator->fails())
            {
                return back()->withErrors(['Name, Valid/Unique Email and Role Needed'])->withInput();
            }

            $user = User::create([
              'name' => $request['name'],
              'email' => $request['email'],
              'password' => Hash::make('1qaz2wsx'),
              'phone' => $request['phone'],

            ]);

            if( $user->id )
            {
              // Save Roles
              $user->roles()->sync($request['role_id']);

              return back()->with('success', 'User has been added successfully!');
            }

            return back()->withErrors(['User Creation Error']);
        }
        catch (Exception $e)
        {
          return back()->withErrors(['User Creation Error : ' . $e->getMessage()]);
        }
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
    public function edit(Request $request, string $id)
    {
        //
        echo "MAJU jabo";
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
