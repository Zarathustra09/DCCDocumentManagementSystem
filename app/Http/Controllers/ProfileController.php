<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('profile.show', compact('user'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->hasFile('profile_image')) {
            // Delete old profile picture if exists
            if ($user->profile_image) {
                Storage::delete('public/' . $user->profile_image);
            }

            $profilePicture = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $profilePicture;
        }

        $user->save();

        return redirect()->route('profile.index')->with('success', 'Profile updated successfully!');
    }

    public function destroy()
    {
        $user = Auth::user();

        // Delete profile picture if exists
        if ($user->profile_image) {
            Storage::delete('public/' . $user->profile_image);
        }

        $user->delete();

        return redirect('/')->with('success', 'Account deleted successfully!');
    }

    public function uploadImage(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('profile_image')) {
            // Delete old profile picture if exists
            if ($user->profile_image) {
                Storage::delete('public/' . $user->profile_image);
            }

            $profilePicture = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $profilePicture;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile picture updated successfully!',
                'image_url' => asset('storage/' . $profilePicture)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No image file found.'
        ], 400);
    }

    public function resetImage(Request $request)
    {
        $user = Auth::user();

        if ($user->profile_image) {
            // Delete the profile picture file
            Storage::delete('public/' . $user->profile_image);

            // Update user record
            $user->profile_image = null;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile picture removed successfully!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No profile picture to remove.'
        ], 400);
    }
}
