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

    $extension = explode('/',explode(';',$base64_ex[0])[0])[1];

    // カンマ以降の文字列
    $fileData =  base64_decode($base64_ex[1]);

    // ファイル名にUUIDを発行
    $filename_body = Str::orderedUuid()->toString();

    // base64の画像データを一時ファイルに保存
    $tmpFilePath = sys_get_temp_dir() . '/' . $filename_body.'.'.$extension;
    file_put_contents($tmpFilePath, $fileData);

    // sftpで画像保存サーバーにファイルを保存
    Storage::disk('sftp')->putFileAs( 'public', $tmpFilePath,$filename_body.'.'.$extension);

    // アクセス用URLをレスポンスで返却
    return response()->json([asset('api/image/'.$filename_body.'.'.$extension)]);

});

Route::get('/image/{filename}', function (Request $request, string $filename) {

    $filedata = Storage::disk('sftp')->get('public/'.$filename);

    $extension = explode('.', $filename)[1];

    return response(($filedata))->header('content-Type', 'image/'. $extension);
});

