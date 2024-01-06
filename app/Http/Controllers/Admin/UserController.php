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
    public function togglestatus(Request $request)
    {
      // Toggle status
      if(isset($request['action']) && $request['action'] == 'togglestatus' &&
         isset($request['item']) && $request['item']!='')
      {
         $user = User::find($request['item']);
         if($user)
         {
            $user->status = $user->status ? 0 : 1;
            $user->save();
            return back()->with('success', 'User Status was changed successfully!');
         }
      }

      return back()->withErrors(['Error occurred while changing status']);
    }

    // Listing
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
        ->paginate(10);

        $roles = Role::where('id', '<>', 1)->get();

        return view('admin.employees.index', compact('employees', 'roles', 'role_id'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::where('id', '<>', 1)->get();

        return view('admin.employees.create', compact('roles'));
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
              'phone'  => $request['phone'],
              'status' => $request['status'] ?? 0
            ]);

            if( $user->id )
            {
              // Save Roles
              $user->roles()->sync($request['role_id']);
              return redirect('/admin/employees')->with('success', 'User Added Successfully.');
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
        $roles = Role::where('id', '<>', 1)->get();

        $employee = User::findOrFail($id);

        return view('admin.employees.edit', compact('roles', 'employee'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try
        {
          //
          // Validate Request
          $rules = [
            'name'      => 'required',
            'email'     => 'required|email|unique:users,email,'.$id.',id',
            'role_id'   => 'required|array|min:1',
          ];

          $validator = Validator::make($request->all(),$rules);

          if($validator->fails())
          {
              return back()->withErrors(['Name, Valid/Unique Email and Role Needed'])->withInput();
          }

          $user = User::findOrFail($id);
          $user->name = $request['name'];
          $user->email = $request['email'];
          $user->phone = $request['phone'];
          $user->status = $request['status'] ?? 0;
          $user->save();

          // Sync Roles
          $user->roles()->sync($request['role_id']);
          return redirect('/admin/employees')->with('success', 'User Updated Successfully.');
        }
        catch (Exception $e)
        {
          return back()->withErrors(['User Updation Error : ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
