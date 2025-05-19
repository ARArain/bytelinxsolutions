<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers;

use App\Traits\CommonFunction;

/**
 * Description of AppController
 *
 * @author Abid Rizvi
 */
class AppController extends Controller
{
    
    use CommonFunction;
    public function __construct()
    {
        // print_r(storage_path());die;
        // common 
        $GET = $this->obtainGet();
        define('SITE_AT', config('app.env'));
        define('SITE_URL', config('app.site_url'));
        define('CANONICAL_URL', config('app.canonical_url'));
        define('ASSETS_URL', config('app.site_assets_url'));
        define('SITE_ASSETS_URL', config('app.site_assets_url'));
        define('SITE_AJAX_URL', SITE_URL . "services/");
        define('IMAGE_URL', SITE_URL . "images/");
        define('CONTACT_EMAIL', "info@prodealmakers.com");
    }
}