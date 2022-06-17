<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\File;

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

    $base64 = $request->input('base64');
    $base64_ex = explode(',',$base64);
    $fileData =  base64_decode($base64_ex[1]);
    $tmpFilePath = sys_get_temp_dir() . '/' . Str::uuid()->toString();
    file_put_contents($tmpFilePath, $fileData);

    $tmpFile = new File($tmpFilePath);

    $filename = 'hoge_'.Str::uuid()->toString().'.png';
    Storage::disk('sftp')->putFileAs( 'public', $tmpFile,$filename);
    //dd(asset('storage/hoge.jpg'));
    //Storage::putFileAs('', $tmpFile,'hoge.jpg');
    return response()->json([asset('api/image/'.$filename)]);

});

Route::get('/image/{filename}', function (Request $request, string $filename) {

    $filedata = Storage::disk('sftp')->get('public/'.$filename);
    return response(($filedata))->header('content-Type', 'image/png');
});

