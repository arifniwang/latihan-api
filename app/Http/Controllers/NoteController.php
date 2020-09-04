<?php

namespace App\Http\Controllers;


use App\Helpers\Niwang;
use Illuminate\Support\Facades\DB;

class NoteController extends Controller
{
	private $user;

	function __construct()
	{
		$validator['user_id'] = 'required|integer';
		Niwang::Validator($validator);	

		$user = DB::table('user')
		->where('id',request('user_id'))
		->first();

		if(!$user){
			$response['api_status'] = 0;
			$response['api_message'] = 'User tidak terdaftar';
			$response['error_code'] = 'Error #0, User is invalid';
			$res = response()->json($response);
			$res->send();
			exit;
			dd('x');
		}

		$this->user = $user;
	}

	public function postSave()
	{
		$validator['note'] = 'required|string|max:255';
		Niwang::Validator($validator);

		$save['created_at'] = Niwang::now();
		$save['user_id'] = $this->user->id;
		$save['note'] = request('note');
		$act = DB::table('note')->insertGetId($save);
		if (!$act) {
			$response['api_status'] = 0;
			$response['api_message'] = 'Terjadi kesalahan, silahkan coba kembali';
			$response['error_code'] = 'Error #1, Failed save data';
		} else {
			$data['id'] = $act;
			$data['note'] = $save['note'];
			$data['created_at'] = $save['created_at'];

			$response['api_status'] = 1;
			$response['api_message'] = 'Data berhasil disimpan';
			$response['data'] = $data;
		}

		return response()->json($response);
	}

	public function getList()
	{
		$data = DB::table('note')
		->select('id','note','created_at')
		->where('user_id',$this->user->id)
		->whereNull('deleted_at')
		->get();

		$response['api_status'] = 1;
		$response['api_message'] = (count($data) > 0 ? 'Data ditemukan' : 'Data kosong');
		$response['data'] = $data;

		return response()->json($response);
	}

	public function postUpdate()
	{
		$validator['note_id'] = 'required|integer';
		$validator['note'] = 'required|string|max:255';
		Niwang::Validator($validator);

		$note = DB::table('note')
		->where('user_id',$this->user->id)
		->where('id',request('note_id'))
		->whereNull('deleted_at')
		->first();

		if (!$note) {
			$response['api_status'] = 0;
			$response['api_message'] = 'Data tidak ditemukan';
			$response['error_code'] = 'Error #1, Data not found';
		} else {
			$save['updated_at'] = Niwang::now();
			$save['note'] = request('note');
			$act = DB::table('note')->where('id',$note->id)->update($save);
			if (!$act) {
				$response['api_status'] = 0;
				$response['api_message'] = 'Terjadi kesalahan, silahkan coba kembali';
				$response['error_code'] = 'Error #2, Failed update data';
			} else {
				$data['id'] = $note->id;
				$data['note'] = $save['note'];
				$data['created_at'] = $note->created_at;

				$response['api_status'] = 1;
				$response['api_message'] = 'Data berhasil di update';
				$response['data'] = $data;
			}
		}

		return response()->json($response);
	}

	public function postDelete()
	{
		$validator['note_id'] = 'required|integer';
		Niwang::Validator($validator);

		$note = DB::table('note')
		->where('user_id',$this->user->id)
		->where('id',request('note_id'))
		->whereNull('deleted_at')
		->first();

		if (!$note) {
			$response['api_status'] = 0;
			$response['api_message'] = 'Data tidak ditemukan';
			$response['error_code'] = 'Error #1, Data not found';
		} else {
			$save['deleted_at'] = Niwang::now();
			$act = DB::table('note')->where('id',$note->id)->update($save);
			if (!$act) {
				$response['api_status'] = 0;
				$response['api_message'] = 'Terjadi kesalahan, silahkan coba kembali';
				$response['error_code'] = 'Error #2, Failed delete data';
			} else {
				$response['api_status'] = 1;
				$response['api_message'] = 'Data berhasil dihapus';
			}
		}

		return response()->json($response);
	}
}









