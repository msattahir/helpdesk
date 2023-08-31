<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public static function change_password_rules(Request $request){
        return [
            'password' => 'required',
            'new_password' => 'required|min:6|different:password',
            'new_password_confirmation' => 'required|same:new_password'
        ];
    }

    public function change_password(Request $request){

        if($request->ajax())
        {
            $request->validate(self::change_password_rules($request));

            $user = auth()->user();

            if (!Hash::check($request->input('password'), $user->password)) {
                return response()->json(['status' => 'danger', 'message' => 'Invalid Password']);
            }

            $user->password = Hash::make($request->input('new_password'));
            $user->save();

            ActivityLog::create([
                'activity' => 'Password changed',
            ]);
            return response()->json(['status' => 'success', 'message' => 'Password updated successfully']);
        }

        return view('change-password');
    }

    public function forgot_password(Request $request){

        if($request->ajax()){
            $credentials = $request->only('staff_no', 'password');
            if (Auth::attempt($credentials)) {
                $request->validate(self::change_password_rules($request));
                $user = auth()->user();

                $user->password = Hash::make($request->input('new_password'));
                $user->reset_password = false;
                $user->save();

                ActivityLog::create([
                    'activity' => 'Password reset',
                ]);
                return response()->json(['status' => 'success', 'message' => 'Password reset successfully']);
            } else {
                return response()->json(['status' => 'danger', 'message' => 'Invalid Password']);
            }
        }

        return view('forgot-password');
    }

    public function reset_password(Request $request){

        if($request->ajax()){
            $credentials = $request->only('staff_no', 'password');
            if (Auth::attempt($credentials)) {
                $request->validate(self::change_password_rules($request));
                $user = auth()->user();

                $user->password = Hash::make($request->input('new_password'));
                $user->reset_password = false;
                $user->save();

                ActivityLog::create([
                    'activity' => 'Password reset',
                ]);
                return response()->json(['status' => 'success', 'message' => 'Password reset successfully']);
            } else {
                return response()->json(['status' => 'danger', 'message' => 'Invalid Password']);
            }
        }

        return view('reset-password', [
            'staff_no' => $request->query('staff_no')
        ]);
    }

    public function login(Request $request){

        if($request->ajax())
        {
            $credentials = $request->only('staff_no', 'password');
            if (Auth::attempt($credentials)) {
                $user = auth()->user();

                if($user->status != 'Active'){
                    auth()->logout();
                    return response()->json([
                        'status' => 'danger',
                        'message' => 'No access, this account is not active.'
                    ]);
                }

                if($user->reset_password){
                    auth()->logout();
                    return response()->json([
                        'status' => 'reset',
                        'staff_no' => $request->staff_no,
                        'message' => 'Reset password first'
                    ]);
                }

                ActivityLog::create([
                    'activity' => 'Logged in'
                ]);

                return response()->json(['status' => 'success', 'message' => 'Log in successfully']);
            } else {
                return response()->json(['status' => 'danger', 'message' => 'Invalid credentials']);
            }
        }

        return view('login');
    }

    public function logout()
    {
        ActivityLog::create([
            'activity' => 'Logged out'
        ]);

        auth()->logout();
        return redirect('/login');
    }
}
