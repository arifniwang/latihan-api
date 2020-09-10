<?php  

namespace App\Helpers;

use Storage;
use Validator;

class Niwang
{
	/**
     * Converting numeric to default size (GB, MB, KB, Bytes, Byte)
     *
     * @param $bytes
     * @return string
     */
    public static function fileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }


	/**
     * Upload image in storage and return path image
     *
     * @param string $name
     * @param string $directory
     * @param integer $max_size
     * @return string
     */
	public static function uploadImage(string $name, $directory = '', $max_size = 1024)
	{
		if (request()->hasFile($name)) {
            //variable
			$file = request()->file($name);
			$ext = $file->getClientOriginalExtension();
			$filename = md5(uniqid()) . '.' . $ext;
			$filesize = $file->getSize();
			$file_path = 'uploads/' . ($directory == '' ? '' : $directory . '/') . date('Y-m');
			$directory_path = base_path('public/' . $file_path);

			if ($filesize < $max_size) {
				$result['api_status'] = 0;
				$result['api_message'] = 'Filesize harus kurang dari ' . Niwang::fileSize($max_size);
				$result['error_code'] = 'uploadImage : Invalid Image Extension';
				$res = response()->json($result);
				$res->send();
				exit;
			} elseif (!in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'svg', 'tif', 'bmp', 'ico', 'webp'])) {
				$result['api_status'] = 0;
				$result['api_message'] = 'Format file harus berupa image';
				$result['error_code'] = 'uploadImage : Invalid Image Extension';
				$res = response()->json($result);
				$res->send();
				exit;
			} else {
                //upload file
				if ($file->move($directory_path, $filename)) {
					return $file_path . '/' . $filename;
				} else {
					$result['api_status'] = 0;
					$result['api_message'] = 'Upload file ' . $name . ' gagal, silahkan coba kembali';
					$result['error_code'] = 'uploadImage : Failed upload image';
					$res = response()->json($result);
					$res->send();
					exit;
				}
			}
		} else {
			return null;
		}
	}

	/**
     * Validate JSON params
     *
     * @param array $params
     * @return bool
     */
	public static function Validator(array $params)
	{
		$validator = Validator::make(request()->all(), $params);

        // callback json
		if ($validator->fails()) {
			$result = array();
			$message = $validator->errors();
			$result['api_status'] = 0;
			$result['api_message'] = $message->all(':message')[0];
			$result['error_code'] = 'Validator : Request is not valid';
			$res = response()->json($result);
			$res->send();
			exit;
		}

		return true;
	}

	/**
     * Calling Now Date & Time
     *
     * @return string
     */
	public static function now()
	{
		return date('Y-m-d H:i:s');
	}

    /**
     * Calling Now Date
     *
     * @return string
     */
    public static function date()
    {
    	return date('Y-m-d');
    }

    /**
     * Calling Now Time
     *
     * @return string
     */
    public static function time()
    {
    	return date('H:i:s');
    }

    /**
     * Testing Helper
     *
     * @return string
     */
    public static function Test()
    {
    	return 'Hello World';
    }
}