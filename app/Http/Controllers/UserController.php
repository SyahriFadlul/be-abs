<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserInformationRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return new UserCollection(User::all());
    }

    public function show($user)
    {
        $user = User::find($user);
        return new UserResource($user);
    }

    public function destroy(User $user)
    {
        $user->profile()->delete();
        $user->address()->delete();
        $user->delete();
        return response()->json("Deleted succesfully");
    }

    public function updateUserInformation(Request $request, $id)
    {
        $data = $request->validate([
            'firstName'=>'required',
            'lastName'=>'required',
            'username'=>'required',
            'email'=>'required',
            'gender'=>'nullable',
            'birthday'=>'nullable',
            'phone_number'=>'nullable',
            'photo_profile'=>'nullable',
        ]);

        $profileData = [
            'firstName' => $data['firstName'],
            'lastName' => $data['lastName'],
            'gender' => $data['gender'],
            'birthday' => $data['birthday'],
            'phone_number' => $data['phone_number'],
            'photo_profile' => $data['photo_profile'],
        ];
        $userData = [
            'username' => $data['username'],
            'email' => $data['email']
        ];

        $user = User::find($id);
        $profile = Profile::where('user_id', $id)->first();
        $user->update($userData);
        $profile->update($profileData);

        return response()->json($data);
    }
}
