<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\AdminBlogController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AuthController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('index');
});

// お問い合わせフォーム
// ①お問い合わせフォームの表示
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
// ②お問い合わせフォームの送信
Route::post('/contact', [ContactController::class, 'sendMail']);
// ③お問い合わせフォームの送信完了
Route::get('/contact/complete', [ContactController::class, 'complete'])->name('contact.complete');


// 管理画面
Route::prefix('/admin')
    ->name('admin.')
    ->group(function () {
        // ログイン時のみアクセス可能なルート
        Route::middleware('auth')
        ->group(function(){

            
            // // １．ブログ
            // Route::get('/blogs',[AdminBlogController::class, 'index'])->name('blogs.index');
            // // ２．ブログ投稿画面
            // Route::get('/blogs/create',[AdminBlogController::class, 'create'])->name('blogs.create');
            // // ３．ブログ投稿処理
            // Route::post('/blogs',[AdminBlogController::class, 'store'])->name('blogs.store');
            // // ４．ブログ編集画面   
            // Route::get('/blogs/{blog}', [AdminBlogController::class, 'edit'])->name('blogs.edit');
            // // ５．ブログ更新処理
            // Route::put('/blogs/{blog}', [AdminBlogController::class, 'update'])->name('blogs.update');
            // // ６．ブログ削除処理
            // Route::delete('/blogs/{blog}', [AdminBlogController::class, 'destroy'])->name('blogs.destroy');
            
            // リソースコントローラーで上記6つのブログ関連のルーティングを置き換えできる
            Route::resource('/blogs', AdminBlogController::class)->except('show');
            // ログアウト
            Route::post('/logout',[AuthController::class, 'logout'])->name('logout');
        });
        // 未ログイン時のみアクセス可能なルート
        Route::middleware('guest')
            ->group(function(){
                // ログイン画面の表示
                Route::get('/login',[AuthController::class, 'showLoginForm'])->name('login');
                // ログイン処理
                Route::post('/login',[AuthController::class, 'login'])->name('login');
            });
    });
        
        
// ユーザー管理
Route::get('/admin/users/create',[UserController::class, 'create'])->name('users.create');
Route::post('/admin/users',[UserController::class, 'store'])->name('users.store');

// // 認証
// // ログイン画面の表示
// Route::get('/admin/login',[AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
// // ログイン処理
// Route::post('/admin/login',[AuthController::class, 'login'])->name('admin.login');


