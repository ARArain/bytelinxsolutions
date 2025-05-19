<?php
/*
$site_url = 'http://localhost:8000/';
$site_assets_url = 'http://localhost:8000/web/';

 */
$app['env'] = 'local';
// echo '<pre>', print_r($_SERVER);die;
if ($_SERVER['HTTP_HOST'] == 'localhost:8000') {
    $app['admin_url'] = 'http://localhost:8000/admin/';
    $app['admin_assets_url'] = 'http://localhost:8000/admin_assets/';
    $app['url'] = 'http://localhost:8000/';

    $app['site_url'] = 'http://localhost:8000/';
    $app['site_assets_url'] = 'http://localhost:8000/assets/';
} else {
    $app['admin_url'] = 'https://www.prodealmakers.com/admin/';
    $app['admin_assets_url'] = 'https://www.prodealmakers.com/admin_assets/';
    $app['url'] = 'https://www.prodealmakers.com/';

    $app['site_url'] = 'https://www.prodealmakers.com/';
    $app['site_assets_url'] = 'https://www.prodealmakers.com/assets/';
}

$app['canonical_url'] = 'https://www.prodealmakers.com';
$app['asset_url'] = null;
$app['cookie_domain'] = ".local";

$app['debug'] = true;
