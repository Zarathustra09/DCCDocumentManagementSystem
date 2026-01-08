<?php

namespace App\Http\Controllers\API\Spears;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    private function authorizeApiKey(Request $request)
    {
        $key = $request->header('X-API-KEY') ?? $request->get('api_key');
        $expected = env('API_KEY');
        if (empty($expected) || $key !== $expected) {
            return response()->json(['message' => 'Unauthorized'], 401)->send();
        }
    }

    public function store(Request $request)
    {
        $this->authorizeApiKey($request);

        $data = $request->validate([
            'employee_no'    => 'required|string|unique:users,employee_no',
            'username'       => 'nullable|string|unique:users,username',
            'password'       => 'nullable|string|min:6',
            'firstname'      => 'nullable|string',
            'middlename'     => 'nullable|string',
            'lastname'       => 'nullable|string',
            'address'        => 'nullable|string',
            'birthdate'      => 'nullable|date',
            'contact_info'   => 'nullable|string',
            'gender'         => 'nullable|string',
            'datehired'      => 'nullable|date',
            'profile_image'  => 'nullable|string',
            'created_on'     => 'nullable|date',
            'barcode'        => 'nullable|string',
            'email'          => 'nullable|email|unique:users,email',
            'separationdate' => 'nullable|date',
            'organization_id'=> 'nullable|integer',
            'department_id'  => 'nullable|integer',
        ]);

        if (isset($data['password']) && is_string($data['password'])) {
            $pwd = $data['password'];
            if (str_starts_with($pwd, '$2y$') || str_starts_with($pwd, '$2b$') || str_starts_with($pwd, '$argon2')) {
                $data['password'] = $pwd;
            } else {
                $data['password'] = $pwd;
            }
        }

        $user = User::create($data);

        return response()->json([
            'message' => 'User created',
            'data'    => $user,
        ], 201);
    }

    public function update(Request $request, $employee_no)
    {
        $this->authorizeApiKey($request);

        $user = User::where('employee_no', $employee_no)->firstOrFail();

        $data = $request->validate([
            'employee_no'    => ['sometimes','string', Rule::unique('users','employee_no')->ignore($user->id)],
            'username'       => ['sometimes','string', Rule::unique('users','username')->ignore($user->id)],
            'password'       => ['sometimes','string','min:6'],
            'firstname'      => 'sometimes|string',
            'middlename'     => 'nullable|string',
            'lastname'       => 'sometimes|string',
            'address'        => 'nullable|string',
            'birthdate'      => 'nullable|date',
            'contact_info'   => 'nullable|string',
            'gender'         => 'nullable|string',
            'datehired'      => 'nullable|date',
            'profile_image'  => 'nullable|string',
            'created_on'     => 'nullable|date',
            'barcode'        => 'nullable|string',
            'email'          => ['nullable','email', Rule::unique('users','email')->ignore($user->id)],
            'separationdate' => 'nullable|date',
            'organization_id'=> 'nullable|integer',
            'department_id'  => 'nullable|integer',
        ]);

        if (array_key_exists('password', $data) && $data['password'] === '') {
            unset($data['password']);
        } elseif (isset($data['password']) && is_string($data['password'])) {
            $pwd = $data['password'];
            if (str_starts_with($pwd, '$2y$') || str_starts_with($pwd, '$2b$') || str_starts_with($pwd, '$argon2')) {
                $data['password'] = $pwd;
            } else {
                $data['password'] = $pwd;
            }
        }

        $user->fill($data);
        $user->save();

        return response()->json([
            'message' => 'User updated',
            'data'    => $user,
        ], 200);
    }
}
