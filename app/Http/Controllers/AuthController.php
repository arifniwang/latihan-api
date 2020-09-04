<?php

namespace App\Http\Controllers;


use App\Helpers\Niwang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
	public function postRegister()
	{
		$validator['email'] = 'required|email|max:255';
		$validator['password'] = 'required|string|max:50';
		$validator['name'] = 'required|string|max:255';
		$validator['phone'] = 'nullable|numeric|digits_between:10,15';
		$validator['image'] = 'nullable|image';
		Niwang::Validator($validator);

		$check = DB::table('user')
		->where('email',request('email'))
		->whereNull('deleted_at')
		->count();
		if ($check > 0) {
			$response['api_status'] = 0;
			$response['api_message'] = 'Email sudah digunakan';
			$response['error_code'] = 'Error #1, Email is registered';
		} else {
			$save['created_at'] = Niwang::now();
			$save['email'] = request('email');
			$save['password'] = Hash::make(request('password'));
			$save['name'] = request('name');
			$save['phone'] = request('phone');
			$save['image'] = Niwang::uploadImage('image');
			$act = DB::table('user')->insertGetId($save);
			if (!$act) {
				$response['api_status'] = 0;
				$response['api_message'] = 'Terjadi kesalahan, silahkan coba kembali';
				$response['error_code'] = 'Error #2, Failed save data';
			} else {
				$data['id'] = $act;
				$data['name'] = $save['name'];
				$data['email'] = $save['email'];
				$data['phone'] = ($save['phone'] == '' ? '' : $save['phone']);
				$data['image'] = ($save['image'] == '' ? '' : url($save['image']));

				$response['api_status'] = 1;
				$response['api_message'] = 'Register berhasil';
				$response['data'] = $data;
			}
		}

		return response()->json($response);
	}

	public function postLogin()
	{
		$validator['email'] = 'required|email|max:255';
		$validator['password'] = 'required|string|max:50';
		Niwang::Validator($validator);

		$user = DB::table('user')
		->select('id','email','password','name','phone','image')
		->where('email',request('email'))
		->whereNull('deleted_at')
		->first();
		if (!$user) {
			$response['api_status'] = 0;
			$response['api_message'] = 'Email tidak terdaftar';
			$response['error_code'] = 'Error #1, Email not registered';
		} elseif (!Hash::check(request('password'), $user->password)) {
			$response['api_status'] = 0;
			$response['api_message'] = 'Email/Password yang anda masukan salah';
			$response['error_code'] = 'Error #2, Entered password is invalid';
		} else {
			$data['id'] = $user->id;
			$data['name'] = $user->name;
			$data['email'] = $user->email;
			$data['phone'] = ($user->phone == '' ? '' : $user->phone);
			$data['image'] = ($user->image == '' ? '' : url($user->image));

			$response['api_status'] = 1;
			$response['api_message'] = 'Selamat datang kembali '.$user->name;
			$response['data'] = $data;
		}
		return response()->json($response);
	}
}









