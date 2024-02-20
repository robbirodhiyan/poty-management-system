<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\authentications\LoginBasic;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/auth', [LoginBasic::class, 'login'])->name('api.login');




Route::get('/list-bank', function (Request $request) {
  $request->validate([
    'code_bank' => 'numeric',
    'limit' => 'numeric',
    'bank' => 'string'
  ]);

  $json = file_get_contents(base_path('resources/views/json/list-bank.json'));

  $data = json_decode($json, true);
  if ($request->code_bank) {
    $data = array_filter($data, function ($item) use ($request) {
      return $item['code'] == $request->code_bank;
    });

    $data = array_values($data);

    if (count($data) == 0) {
      return response()->json([
        'status' => false,
        'message' => 'Data not found'
      ]);
    }

    // return response()->json([
    //     'status' => true,
    //     'data' => $data
    // ]);
  }

  if ($request->bank) {
    $data = array_filter($data, function ($item) use ($request) {
      return strpos(strtolower($item['name']), strtolower($request->bank)) !== false;
    });

    $data = array_values($data);

    if (count($data) == 0) {
      return response()->json([
        'status' => false,
        'message' => 'Data not found'
      ]);
    }

    // return response()->json([
    //     'status' => true,
    //     'data' => $data
    // ]);
  }

  if ($request->limit) {
    $data = array_slice($data, 0, $request->limit);
  }

  return response([
    'status' => true,
    'data' => $data
  ]);
})->name('api.list.bank');

// Route::get('/user', [LoginBasic::class,'user'])->name('api.user');

use App\Http\Controllers\APIController;

// Authentication
Route::post('/login', [APIController::class, 'login']);
Route::post('/register', [APIController::class, 'register']);

// Routes for Owner role
Route::middleware(['auth:api', 'role:owner'])->group(function () {
  $controller_path = 'App\Http\Controllers\APIController';

  Route::get('/notes', [$controller_path, 'createNote'])->name('api.createNote');
  Route::get('/notes/edit/{id}', [$controller_path, 'editNote'])->name('api.editNote');
  Route::put('/notes/update/{id}', [$controller_path, 'updateNote'])->name('api.updateNote');
  Route::delete('/notes/delete/{id}', [$controller_path, 'deleteNote'])->name('api.deleteNote');
  Route::get('/dashboard', [$controller_path, 'index'])->name('api.dashboard');
  Route::post('/dashboard/notes', [$controller_path, 'storeNote'])->name('api.dashboard.storeNote');

  // Add other routes for the 'owner' role...

  // Example transaction routes
  Route::get('/transaction/summary', [$controller_path, 'getTransactionSummary'])->name('api.summary');
  Route::get('/transaction/pemasukan', [$controller_path, 'getPemasukan'])->name('api.pemasukan');
  Route::post('/transaction/pemasukan', [$controller_path, 'storePemasukan'])->name('api.pemasukan.store');
  // Add other transaction routes...

  // Example product routes
  Route::get('/product', [$controller_path, 'getProducts'])->name('api.product');
  Route::post('/product/input/store', [$controller_path, 'storeProduct'])->name('api.storeproduct');
  // Add other product routes...

  // Example production routes
  Route::get('/production', [$controller_path, 'getProductions'])->name('api.production');
  Route::post('/production/input/store', [$controller_path, 'storeProduction'])->name('api.storeproduction');
  // Add other production routes...
});

Route::middleware(['auth:api', 'role:admin'])->group(function () {
  $controller_path = 'App\Http\Controllers\APIController';

  Route::get('/notes', [$controller_path, 'createNote'])->name('api.createNote');
  Route::get('/notes/edit/{id}', [$controller_path, 'editNote'])->name('api.editNote');
  Route::put('/notes/update/{id}', [$controller_path, 'updateNote'])->name('api.updateNote');
  Route::delete('/notes/delete/{id}', [$controller_path, 'deleteNote'])->name('api.deleteNote');
  Route::get('/dashboard', [$controller_path, 'index'])->name('api.dashboard');
  Route::post('/dashboard/notes', [$controller_path, 'storeNote'])->name('api.dashboard.storeNote');

  // Add other routes for the 'owner' role...

  // Example transaction routes
  Route::get('/transaction/summary', [$controller_path, 'getTransactionSummary'])->name('api.summary');
  Route::get('/transaction/pemasukan', [$controller_path, 'getPemasukan'])->name('api.pemasukan');
  Route::post('/transaction/pemasukan', [$controller_path, 'storePemasukan'])->name('api.pemasukan.store');
  // Add other transaction routes...

  // Example product routes
  Route::get('/product', [$controller_path, 'getProducts'])->name('api.product');
  Route::post('/product/input/store', [$controller_path, 'storeProduct'])->name('api.storeproduct');
  // Add other product routes...

  // Example production routes
  Route::get('/production', [$controller_path, 'getProductions'])->name('api.production');
  Route::post('/production/input/store', [$controller_path, 'storeProduction'])->name('api.storeproduction');
  // Add other production routes...

  // Example dashboard route
  Route::get('/dashboard', [$controller_path, 'adminDashboard'])->name('api.admin.dashboard');
});

// Main Page Route
Route::middleware('auth:api')->get('/dashboard-analytics', [APIController::class, 'dashboardAnalytics'])->name('api.dashboard-analytics');
