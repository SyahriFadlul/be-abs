<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Http\Resources\ProfileCollection;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\V1\ProductResource;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;

class ProfilesController extends Controller
{
    public function index()
    {
        return new ProfileCollection(Profile::all());
    }

    public function showUserProfile($id)
    {
        $profile = Profile::where('user_id', $id)->get();
        return new ProfileCollection($profile);
    }

    public function show( $profile)
    {               
        $profile = Profile::where('user_id', $profile)->first();
        $profile->photo_profile = isset($profile->photo_profile) ? $profile->photo_profile : 'images/default_avatar.jpg';
        return response()->json($profile);
    }

    public function store(ProfileRequest $request)
    {
        $data = $request->validated();
        Profile::create($data);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'firstName' => 'nullable',
            'lastName' => 'nullable',
            'gender' => 'nullable',
            'birthday' => ['nullable', 'date'],
            'phone_number' => 'nullable',
            'photo_profile' => 'nullable|image|mimes:jpeg,jpg,png|max:3000'
        ]);

        $profile = Profile::where('user_id', $id)->first();
        $data = $request->except('photo_profile');

        if ($request->hasFile('photo_profile')) {
            $imagePath = $request->file('photo_profile')->store('images', 'public');
            $data['photo_profile'] = $imagePath;
        } 

        $profile->update($data);
        return response()->json($profile);
    }

    public function delete()
    {
    }
}
