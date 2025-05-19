<?php

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
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FormsController;

route::get('/','HomeController@index');
route::get('video','HomeController@video');
route::get('privacy-policy','HomeController@privacyPolicy');
route::get('terms-and-conditions','HomeController@termsAndConditions');
route::get('refund-and-cancelation','HomeController@refundAndCancelation');
route::get('site-map','HomeController@siteMap');
Route::get('sitemap.xml', 'HomeController@siteMapXml');
route::any('save-contact-form','FormsController@saveContactForm');
