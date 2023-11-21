<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    protected function storeImage(string $field, $dataUser)
    {
        if ($dataUser[$field]) {
            $nameFile = explode('/', $dataUser[$field]);
            Storage::delete("document/$field/" . end($nameFile));
        }
        $newName =  request()->file($field)->hashName("document/$field/");
        $path = request()->file($field)->storeAs($newName);
        // Storage::putFileAs("document/$field", request()->file($field), $newName);
        $url = Storage::url($path);
        return $url;
    }

    public function updateProfile(Request $request)
    {
        $id = request()->user()->id;
        $rules = [
            'name' => 'required',
            'email' => "required|unique:users,email,$id",
            'ktp' => 'mimes:pdf,jpg,jpeg,png',
            'ijazah' => 'mimes:pdf,jpg,jpeg,png',
            'cv' => 'mimes:pdf,jpg,jpeg,png',
            'avatar' => 'mimes:pdf,jpg,jpeg,png',
        ];
        $data = [
            'name' => $request['name'],
            'email' => $request['email'],
            'ktp' => $request['ktp'],
            'ijazah' => $request['ijazah'],
            'cv' => $request['cv'],
            'avatar' => $request['avatar'],
        ];

        $validator = Validator::make($data, $rules);
        $validator->validate();

        $dataUser = User::find($id);

        if ($request->file('ktp')) {
            $data['ktp'] = $this->storeImage('ktp', $dataUser);
        }
        if ($request->file('cv')) {
            $data['cv'] = $this->storeImage('cv', $dataUser);
        }
        if ($request->file('ijazah')) {
            $data['ijazah'] = $this->storeImage('ijazah', $dataUser);
        }
        if ($request->file('avatar')) {
            $data['avatar'] = $this->storeImage('avatar', $dataUser);
        }

        User::where('id', $id)
            ->update($data);

        return response()->json([
            'status' => true,
            'message' => 'update data user berhasil',
        ], 200);
    }
}
