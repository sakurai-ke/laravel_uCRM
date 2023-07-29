<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\InertiaTestController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PurchaseController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes ffor your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// 商品管理
// ->middleware(['auth', 'verified'])について、認証されていないと表示されなくなるようにしている
Route::resource('items', ItemController::class)
->middleware(['auth', 'verified']);

// 顧客管理
Route::resource('customers', CustomerController::class)
->middleware(['auth', 'verified']);

// 購入画面
Route::resource('purchases', PurchaseController::class)
->middleware(['auth', 'verified']);

// コントローラーを経由せずにビューの渡している
Route::get('/inertia-test', function () {
    return Inertia::render('InertiaTest');
    });

Route::get('/component-test', function () {
    return Inertia::render('ComponentTest');
    });
    
Route::get('/inertia/index', [InertiaTestController::class, 'index'])->name('inertia.index');
Route::get('/inertia/create', [InertiaTestController::class, 'create'])->name('inertia.create');
Route::post('/inertia', [InertiaTestController::class, 'store'])->name('inertia.store');
Route::get('/inertia/show/{id}', [InertiaTestController::class, 'show'])->name('inertia.show');
Route::delete('/inertia/{id}', [InertiaTestController::class, 'delete'])->name('inertia.delete');

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
