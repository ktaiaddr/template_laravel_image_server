<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\File;
use Illuminate\Support\Facades\Storage;
use Rorecek\Ulid\Facades\Ulid;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/image', function (Request $request) {

    // base64文字列（data:image/png;base64,iVBORw～～）を受取り
    $base64 = $request->input('base64');

    // カンマで分割
    $base64_ex = explode(',',$base64);

    // カンマ以降の文字列
    $fileData =  base64_decode($base64_ex[1]);

    $filename = Str::orderedUuid()->toString();

    // 保存先パスを取得
    $tmpFilePath = sys_get_temp_dir() . '/' . $filename.'.png';

    file_put_contents($tmpFilePath, $fileData);

    Storage::disk('sftp')->putFileAs( 'public', $tmpFilePath,$filename.'.png');

    return response()->json([asset('api/image/'.$filename)]);

});

Route::get('/image/{filename}', function (Request $request, string $filename) {

    $filedata = Storage::disk('sftp')->get('public/'.$filename.'.png');

    return response(($filedata))->header('content-Type', 'image/png');
});

