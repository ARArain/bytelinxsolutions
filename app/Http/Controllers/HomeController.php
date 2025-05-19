<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends SiteController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [];
        $data['page'] = 'welcome';
        $data['canonical'] = '';
        return $this->renderT('welcome',$data);
    }
    
    public function video()
    {
        $data = [];
        $data['page'] = 'video';
        $data['canonical'] = 'video';
        return $this->renderT('video',$data);
    }

    public function privacyPolicy()
    {
        $data = [];
        $data['page'] = 'video';
        $data['canonical'] = 'privacy-policy';
        return $this->renderT('privacy-policy',$data);
    }

    public function termsAndConditions()
    {
        $data = [];
        $data['page'] = 'video';
        $data['canonical'] = 'terms-and-conditions';
        return $this->renderT('terms-and-conditions',$data);
    }

    public function refundAndCancelation()
    {
        $data = [];
        $data['page'] = 'video';
        $data['canonical'] = 'refund-and-cancelation';
        return $this->renderT('refund-and-cancelation',$data);
    }

    public function siteMap()
    {
        $data = [];
        $data['page'] = 'video';
        $this->setMeta([
            'title' => 'sitemap',
        ]);
        $data['metaData']['title_com'] = 'Site Map';
        $data['metaData']['kw_com'] = 'Site Map';
        $data['metaData']['description_com'] = 'Site Map to track all proper links for a site.';
        $data['common'] = array(
            array("Home", ""),
            array("Walkthrough Video", "video"),
            array("Privacy Policy", "privacy-policy"),
            array("Terms and Conditions", "terms-and-conditions"),
            array("Refund and Cancelation Policy", "refund-and-cancelation"),
            // array("Home", ""),
        );
        $data['canonical'] = 'site-map';
        return $this->renderT('site-map',$data);
    }

    public function siteMapXml()
    {
        $data['all'] = array();
        $data['all'][] = array("Home", "");
        $data['all'][] = array("Walkthrough Video", "video");
        $data['all'][] = array("Privacy Policy", "privacy-policy");
        $data['all'][] = array("Terms and Conditions", "terms-and-conditions");
        $data['all'][] = array("Refund and Cancelation Policy", "refund-and-cancelation");
        $data['all'][] = array("Site Map", "site-map");
        // $data['all'][] = arr;
        // echo '<pre>', print_r($data);die;
        return response()->view('home.sitemapXml', [
            'posts' => $data,
        ])->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
