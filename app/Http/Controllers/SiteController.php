<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Traits\CommonFunction;

/**
 * Description of SiteController
 *
 * @author Abid Rizvi
 */
class SiteController extends AppController
{
    use CommonFunction;

    protected $menu = [];

    public function __construct()
    {
        parent::__construct(); 
    }
}