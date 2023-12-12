<?php

// use controller;


use App\Http\Controllers\employee\EmployeController;
use App\Http\Controllers\employee\PositionController;
use App\Http\Controllers\payroll\CompensationController;
use App\Http\Controllers\transaction\pemasukan;
use App\Http\Controllers\transaction\summary;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

$controller_path = 'App\Http\Controllers';


Route::get('/auth', function () {
  // return view('content.authentications.auth-login-basic'); // You can replace 'auth.login' with the actual view name for your login page
  return view('content.authentications.auth-login-basic'); // You can replace 'auth.login' with the actual view name for your login page
})->name('auth')->middleware('guest');



Route::middleware(['auth'])->group(function () {
  // Dashboard Route
  $controller_path = 'App\Http\Controllers';
  Route::get('/dashboard', $controller_path . '\dashboard\Analytics@index')->name('dashboard');
  Route::post('/dashboard/notes', $controller_path . '\dashboard\Analytics@storeNote')->name('dashboard.storeNote');
  Route::get('/notes', $controller_path . '\dashboard\Analytics@createNote')->name('createNote');

  //transaction
  Route::get('/transaction/summary', $controller_path . '\transaction\summary@index')->name('summary');
  Route::get('/transactions/datatables', [summary::class, 'index'])->name('transactions.datatables');
  Route::get('/transaction/pemasukan', $controller_path . '\transaction\pemasukan@index')->name('pemasukan');
  Route::post('/transaction/pemasukan', $controller_path . '\transaction\pemasukan@index')->name('pemasukan.store');
  Route::get('transaction/pemasukan/input', $controller_path . '\transaction\pemasukan@create')->name('InputDebit');
  Route::post('transaction/pemasukan/input/store', $controller_path . '\transaction\pemasukan@store')->name('storedebit');

  Route::get('/transaction/pemasukan/edit/{debit}', $controller_path . '\transaction\pemasukan@edit')->name('editpemasukan');
  Route::post('transaction/pemasukan/edit/update', $controller_path . '\transaction\pemasukan@update')->name('updatedebit');
  Route::put('transaction/pemasukan/edit/update/{debit}', $controller_path . '\transaction\pemasukan@update')->name('updatedebit');

  Route::get('/transaction/pengeluaran', $controller_path . '\transaction\pengeluaran@index')->name('pengeluaran');
  Route::get('transaction/pengeluaran/input', $controller_path . '\transaction\pengeluaran@create')->name('InputCredit');
  Route::post('transaction/pengeluaran/input/store', $controller_path . '\transaction\pengeluaran@store')->name('storecredit');

  Route::get('/transaction/pengeluaran/edit/{credit}', $controller_path . '\transaction\pengeluaran@edit')->name('editpengeluaran');
  Route::post('transaction/pengeluaran/edit/update', $controller_path . '\transaction\pengeluaran@update')->name('updatecredit');
  Route::put('transaction/pengeluaran/edit/update/{credit}', $controller_path . '\transaction\pengeluaran@update')->name('updatecredit');

  //produk
  Route::get('/product', $controller_path . '\products\ProductController@index')->name('product');
  Route::get('/product/input', $controller_path . '\products\ProductController@create')->name('inputproduct');
  Route::post('/product/input/store', $controller_path . '\products\ProductController@store')->name('storeproduct');
  Route::get('/product/edit/{product}', $controller_path . '\products\ProductController@edit')->name('editproduct');
  Route::put('/product/edit/update/{product}', $controller_path . '\products\ProductController@update')->name('updateproduct');
  Route::delete('/deleteproduct/{product}', $controller_path . '\products\ProductController@delete')->name('deleteproduct');
  Route::delete('/product/delete/{id}', 'products\ProductController@destroy')->name('product.destroy');
  Route::post('/get-produksi', 'ProductController@getProduksi')->name('get-produksi');
  Route::get('product/generate-pdf', $controller_path . '\products\ProductController@generatePDF')->name('generate-pdf-product');
  Route::post('/update-stok', $controller_path . '\transaction\pemasukan@updateStok')->name('updateStok');

  //produksi
  Route::get('/production', $controller_path . '\productions\ProductionController@index')->name('production');
  Route::get('/production/input/{name_product?}', $controller_path . '\productions\ProductionController@create')->name('inputproduction');
  Route::get('/create-production/{name}', $controller_path . '\productions\ProductionController@create')->name('create-production-with-product');
  Route::post('/production/input/store', $controller_path . '\productions\ProductionController@store')->name('storeproduction');
  Route::get('/production/edit/{production}', $controller_path . '\productions\ProductionController@edit')->name('editproduction');
  Route::put('/production/edit/update/{production}', $controller_path . '\productions\ProductionController@update')->name('updateproduction');
  Route::delete('/deleteproduction/{production}', $controller_path . '\productions\ProductionController@delete')->name('deleteproduction');
Route::get('production/generate-pdf', $controller_path . '\productions\ProductionController@generatePDF')->name('generate-pdf');

  //Lainnya
  Route::get('/lainnya/kategori', $controller_path . '\lainnya\kategori@index')->name('kategori');
  Route::get('/lainnya/kategori/input', $controller_path . '\lainnya\kategori@create')->name('InputKategori');
  Route::post('lainnya/kategori/input/store', $controller_path . '\lainnya\kategori@store')->name('storekategori');

  Route::get('/lainnya/sumber', $controller_path . '\lainnya\sumber@index')->name('sumber');
  Route::get('/lainnya/sumber/input', $controller_path . '\lainnya\sumber@create')->name('InputSumber');
  Route::post('lainnya/sumber/input/store', $controller_path . '\lainnya\sumber@store')->name('storesumber');

  Route::resource('calendar-notes', 'CalendarNoteController');
  Route::get('/getProductData', $controller_path . '\products\ProductController@getProductData')->name('getProductData');

  // Route::get('/tes', [pemasukan::class,'listS3Files']);
  Route::get('/employee/archived', [EmployeController::class, 'archived'])->name('employee.archived');
  Route::post('/employee/unarchived/{nip}', [EmployeController::class, 'unarchived'])->name('employee.unarchived');
  Route::resource('/employee', EmployeController::class);

  Route::resource('/positions', PositionController::class);

  Route::resource('/payroll/compensations', CompensationController::class);
});

// Main Page Route
Route::get('/', $controller_path . '\dashboard\Analytics@index')->name('dashboard-analytics')->middleware('auth');




// // layout
// Route::get('/layouts/without-menu', $controller_path . '\layouts\WithoutMenu@index')->name('layouts-without-menu');
// Route::get('/layouts/without-navbar', $controller_path . '\layouts\WithoutNavbar@index')->name('layouts-without-navbar');
// Route::get('/layouts/fluid', $controller_path . '\layouts\Fluid@index')->name('layouts-fluid');
// Route::get('/layouts/container', $controller_path . '\layouts\Container@index')->name('layouts-container');
// Route::get('/layouts/blank', $controller_path . '\layouts\Blank@index')->name('layouts-blank');

// // pages
// Route::get('/pages/account-settings-account', $controller_path . '\pages\AccountSettingsAccount@index')->name('pages-account-settings-account');
// Route::get('/pages/account-settings-notifications', $controller_path . '\pages\AccountSettingsNotifications@index')->name('pages-account-settings-notifications');
// Route::get('/pages/account-settings-connections', $controller_path . '\pages\AccountSettingsConnections@index')->name('pages-account-settings-connections');
// Route::get('/pages/misc-error', $controller_path . '\pages\MiscError@index')->name('pages-misc-error');
// Route::get('/pages/misc-under-maintenance', $controller_path . '\pages\MiscUnderMaintenance@index')->name('pages-misc-under-maintenance');

// // authentication
// Route::get('/auth/login-basic', $controller_path . '\authentications\LoginBasic@index')->name('auth-login-basic');
// Route::get('/auth/register-basic', $controller_path . '\authentications\RegisterBasic@index')->name('auth-register-basic');
// Route::get('/auth/forgot-password-basic', $controller_path . '\authentications\ForgotPasswordBasic@index')->name('auth-reset-password-basic');

// // cards
// Route::get('/cards/basic', $controller_path . '\cards\CardBasic@index')->name('cards-basic');

// // User Interface
// Route::get('/ui/accordion', $controller_path . '\user_interface\Accordion@index')->name('ui-accordion');
// Route::get('/ui/alerts', $controller_path . '\user_interface\Alerts@index')->name('ui-alerts');
// Route::get('/ui/badges', $controller_path . '\user_interface\Badges@index')->name('ui-badges');
// Route::get('/ui/buttons', $controller_path . '\user_interface\Buttons@index')->name('ui-buttons');
// Route::get('/ui/carousel', $controller_path . '\user_interface\Carousel@index')->name('ui-carousel');
// Route::get('/ui/collapse', $controller_path . '\user_interface\Collapse@index')->name('ui-collapse');
// Route::get('/ui/dropdowns', $controller_path . '\user_interface\Dropdowns@index')->name('ui-dropdowns');
// Route::get('/ui/footer', $controller_path . '\user_interface\Footer@index')->name('ui-footer');
// Route::get('/ui/list-groups', $controller_path . '\user_interface\ListGroups@index')->name('ui-list-groups');
// Route::get('/ui/modals', $controller_path . '\user_interface\Modals@index')->name('ui-modals');
// Route::get('/ui/navbar', $controller_path . '\user_interface\Navbar@index')->name('ui-navbar');
// Route::get('/ui/offcanvas', $controller_path . '\user_interface\Offcanvas@index')->name('ui-offcanvas');
// Route::get('/ui/pagination-breadcrumbs', $controller_path . '\user_interface\PaginationBreadcrumbs@index')->name('ui-pagination-breadcrumbs');
// Route::get('/ui/progress', $controller_path . '\user_interface\Progress@index')->name('ui-progress');
// Route::get('/ui/spinners', $controller_path . '\user_interface\Spinners@index')->name('ui-spinners');
// Route::get('/ui/tabs-pills', $controller_path . '\user_interface\TabsPills@index')->name('ui-tabs-pills');
// Route::get('/ui/toasts', $controller_path . '\user_interface\Toasts@index')->name('ui-toasts');
// Route::get('/ui/tooltips-popovers', $controller_path . '\user_interface\TooltipsPopovers@index')->name('ui-tooltips-popovers');
// Route::get('/ui/typography', $controller_path . '\user_interface\Typography@index')->name('ui-typography');

// // extended ui
// Route::get('/extended/ui-perfect-scrollbar', $controller_path . '\extended_ui\PerfectScrollbar@index')->name('extended-ui-perfect-scrollbar');
// Route::get('/extended/ui-text-divider', $controller_path . '\extended_ui\TextDivider@index')->name('extended-ui-text-divider');

// // icons
// Route::get('/icons/boxicons', $controller_path . '\icons\Boxicons@index')->name('icons-boxicons');

// // form elements

// Route::get('/forms/basic-inputs', $controller_path . '\form_elements\BasicInput@index')->name('forms-basic-inputs');
// Route::get('/forms/input-groups', $controller_path . '\form_elements\InputGroups@index')->name('forms-input-groups');

// // form layouts
// Route::get('/form/layouts-vertical', $controller_path . '\form_layouts\VerticalForm@index')->name('form-layouts-vertical');
// Route::get('/form/layouts-horizontal', $controller_path . '\form_layouts\HorizontalForm@index')->name('form-layouts-horizontal');

// // tables
// Route::get('/tables/basic', $controller_path . '\tables\Basic@index')->name('tables-basic');
