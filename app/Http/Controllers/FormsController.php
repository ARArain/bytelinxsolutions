<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers;

/**
 * Description of FormsController
 *
 * @author Abid Rizvi
 */
class FormsController extends AppController
{
    public function saveContactForm() {
        // echo 'Here';die;
        if (request()->isMethod('post')) {
            $POST = $this->obtainPost();
            // echo '<pre>',print_r($POST);die;
            $data['name'] = isset($POST['name']) && $POST['name'] != '' ? ucwords($POST['name']) : 'Customer';
            $data['message'] = isset($POST['message']) && $POST['message'] != '' ? trim($POST['message']) : 'Please contact me at given detail';
            $data['email'] = isset($POST['email']) && $POST['email'] != '' ? trim($POST['email']) : '';
            $emailArr = array(
                'data' => $data,
                'date_created' => date('Y-m-d H:i:s'),
                'fullName' => $POST['name'],
                'senderEmail' => $POST['email'],
                'senderName' => $POST['name'],
            );
            // echo '<pre>', print_r($emailArr);die;
            $email = $this->sendEmailQuote([
                'email' => 'info@prodealmakers.com',
                'subject' => 'ProDealMakers.com New Inquiry',
                // 'view' => 'rawHtmlReplied',
                'view' => 'contact-email',
                'data' => $emailArr,
            ]);
            $response = ['code' => 11, "message" => "Message Sent Successfully"];
            return $this->jsonResponse($response);
        }
        // }

        exit('after');
    }
    public function createQuote()
    {
        $response = array();
        $oQuoteLog = new \App\Models\QuoteLog();
        if ($this->isPost()) {
            $POST = $this->obtainPost();
                // echo '<pre>',print_r($POST);die;
                if (empty($POST['answer'])) {
                    $response = ['code' => 22, 'key' => 'answer', "message" => "Answer required"];
                }else{
                    if($POST['answer'] != 9){
                        $response = ['code' => 22,'key' => 'answer',  "message" => "Invalid value"];
                    }
                }
                $utm_source = (!empty($POST['utm_source']) ? $POST['utm_source'] : null);
                if (empty($utm_source) || $utm_source == null) {
                    $POST['cookie_utm_source'] = $utm_source = (!empty($_COOKIE['utm_source']) ? $_COOKIE['utm_source'] : null);
                }
                $utm_source = (!empty($utm_source) ? $utm_source : 'organic');
                if (empty($POST['email'])) {
                    $response = ['code' => 22, 'key' => 'email', "message" => "* Email required!"];
                } else if (!empty($POST['email']) && !filter_var($POST['email'], FILTER_VALIDATE_EMAIL)) {
                    $response = ['code' => 22, 'key' => 'email', "message" => "* Email not valid!"];
                } 

                if (isset($response['code']) && $response['code'] == 22) {
                    $logData = array(
                        'code' => $response['code'],
                        'message' => $response['message'],
                        'form_type' => 'Main-Form',
                        'data' => json_encode($POST),
                        'ip' => request()->ip(),
                    );
                    $logData['lastLogId'] = $oQuoteLog->insert($logData);
                    $emailData = array(
                        'id' => $logData['lastLogId'],
                        'senderEmail' => isset($POST['email']) && $POST['email'] != '' ? strtolower($POST['email']) : QUOTES_EMAIL,
                        'fullName' => isset($POST['full_name']) && $POST['full_name'] != '' ? $POST['full_name'] : 'CustomBoxLine',
                        'referrer' => $POST['referrer'],
                        'name' => $logData['form_type'] . 'Error Log!',
                        'message' => $logData['message'],
                        'source' => CURR_EXT != '' ? CURR_EXT : 'COM',
                        'currency' => CURR_EXT != '' && CURR_EXT == 'CO.UK' ? '£' : '$',
                        'date' => CURR_EXT != '' && CURR_EXT == 'COM' ? date('m/d/Y') : date('d/m/Y'),
                        'code' => isset($logData['code']) && $logData['code'] == 22 ? 'Empty or Invalid Field Value' : 'Error!',
                        'ip' => isset($logData['ip']) && $logData['ip'] != '' ? $logData['ip'] : 'IP ADDRESS Not Found',
                        'utm_source' => (!empty($utm_source) ? $utm_source : 'organic'),
                    );
                    // echo '<pre>',print_r($emailData);die;
                    $this->sendEmail([
                        'email' => QUOTES_EMAIL,
                        'subject' => 'CustomBoxline Quote Log Error!',
                        'view' => 'error_log_email',
                        'data' => $emailData,
                        'senderName' => 'CustomBoxline',
                    ]);
                    return $this->jsonResponse($response);
                    exit;
                }
                // echo '<pre>',print_r($POST);die;
                $oCbUsers = new \App\Models\CbUsers();
                $isUserExist = $oCbUsers->finds([
                    'whereClause' => 'email = ?',
                    'whereParams' => [strtolower($POST['email'])],
                ]);
                $customerName = '';
                if (!empty($POST['full_name'])) {
                    $customerName = $POST['full_name'];
                } else {
                    $emailChunks = explode('@', strtolower($POST['email']));
                    $customerName = $emailChunks[0];
                }
                if (empty($isUserExist->id)) {
                    $userId = $oCbUsers->insert([
                        'email' => strtolower($POST['email']),
                        'full_name' => ucwords($customerName),
                        'contact_no' => isset($POST['contact_no']) && $POST['contact_no'] != '' ? $POST['contact_no'] : '',
                        'created_on' => date('Y-m-d H:i:s'),
                        'source' => CURR_EXT != '' ? strtolower(CURR_EXT) : 'com',
                        'ip' => request()->ip(),
                        'utm_source' => (!empty($utm_source) ? $utm_source : 'organic'),
                    ]);
                } else {
                    $userId = $isUserExist->id;
                }
                $fileName = null;
                if (!empty($POST['frmImage'])) {
                    $file = TEMP_PATH . trim($POST['frmImage'], ',');
                    if (file_exists($file)) {
                        $ext = pathinfo($file, PATHINFO_EXTENSION);
                        $new_file_name = time() . '_' . rand(111111, 999999) . '.' . $ext;
                        if (copy($file, SITE_IMAGE_QUOTE_URL . $new_file_name)) {
                            $fileName = $new_file_name;
                            @unlink($file);
                        }
                    }
                }
                // echo '<pre>', print_r(QUOTE_DOCS);die;
                $quote_number = rand(000000000, 999999999);
                $oWebQuotations = new \App\Models\WebQuotations();
                $status = $this->getQuoteStatus(CURR_EXT);
                $inArr = [
                    'quote_number' => $quote_number,
                    'image' => $fileName,
                    'referrer' => $POST['referrer'],
                    'product_id' => $POST['product_id'],
                    'comments' => trim($POST['comments']),
                    'source' => CURR_EXT != '' ? CURR_EXT : 'COM',
                    'status' => $status,
                    'user_id' => trim($userId),
                    'ip' => request()->ip(),
                    'utm_source' => (!empty($utm_source) ? $utm_source : 'organic'),
                    'type' => 'Product',
                    'date_created' => date('Y-m-d'),
                ];
                $lastInsertedId = $oWebQuotations->insert($inArr);
                $WebQuotationsData = $oWebQuotations->findByPK($lastInsertedId);
                $addon = '';
                if (isset($POST['addon']) && count($POST['addon']) > 0) {
                    $addon = implode(',', $POST['addon']);
                }

                $colors = '';
                $oIndustryList = new \App\Models\IndustryList();
                if (isset($POST['print']) && $POST['print'] != '') {
                    $colors = $oIndustryList->getColorsForWebQuotes($POST);
                }
                $oWebSubQuotations = new \App\Models\WebSubQuotations();
                if (empty($POST['q1']) && empty($POST['q2']) && empty($POST['q3'])) {
                    $quantity1 = 100;
                }
                if (isset($POST['q1']) && !empty($POST['q1'])) {
                    $quantity1 = $this->checkNumberLength($POST['q1']);
                }else{
                    $quantity1 = 100;
                }
                // echo $quantity1;die;
                $oYourQuoteHistory = new \App\Models\YourQuoteHistory();
                $oYourQuoteHistory->insertIntoQuotes($POST,$lastInsertedId);
                if (isset($quantity1) && $quantity1 != '') {
                    $subArr = [
                        'user_id' => trim($userId),
                        'quote_id' => $lastInsertedId,
                        'quote_number' => $quote_number,
                        'unit' => isset($POST['unit']) && $POST['unit'] != '' ? strtolower($POST['unit']) : '',
                        'length' => isset($POST['length']) && $POST['length'] != '' ? trim($POST['length']) : 0,
                        'width' => isset($POST['width']) && $POST['width'] != '' ? trim($POST['width']) : 0,
                        'height' => isset($POST['height']) && $POST['height'] != '' ? trim($POST['height']) : 0,
                        'type' => isset($POST['product_style']) && $POST['product_style'] != '' ? $POST['product_style'] : '',
                        'quantity' => $quantity1,
                        'color' => isset($colors) && is_array($colors) ? implode(',', $colors): '',
                        'print' => isset($POST['print']) && $POST['print'] != '' ? $POST['print'] : '',
                        'stock' => isset($POST['stock']) && $POST['stock'] != '' ? $POST['stock'] : '',
                        'coating' => isset($POST['coat']) && $POST['coat'] != '' ? $POST['coat'] : '',
                        'add_ons' => $addon,
                        'currency' => $this->getQuoteCurrency(CURR_EXT),
                        'conversion_rate' => $this->getQuoteConversionRate(CURR_EXT),
                        'ip' => request()->ip(),
                        'utm_source' => (!empty($utm_source) ? $utm_source : 'organic'),
                    ];
                    // echo '<pre>', print_r($subArr);die;
                    $oWebSubQuotations->insert($subArr);
                }
                $quantity2 = $this->checkNumberLength($POST['q2']);
                if ($this->check2ndQuantity($quantity1,$quantity2) == true) {
                    $subArr = [
                        'user_id' => trim($userId),
                        'quote_id' => $lastInsertedId,
                        'quote_number' => $quote_number,
                        'unit' => isset($POST['unit']) && $POST['unit'] != '' ? strtolower($POST['unit']) : '',
                        'length' => isset($POST['length']) && $POST['length'] != '' ? trim($POST['length']) : 0,
                        'width' => isset($POST['width']) && $POST['width'] != '' ? trim($POST['width']) : 0,
                        'height' => isset($POST['height']) && $POST['height'] != '' ? trim($POST['height']) : 0,
                        'type' => isset($POST['product_style']) && $POST['product_style'] != '' ? $POST['product_style'] : '',
                        'quantity' => $quantity2,
                        'color' => isset($colors) && is_array($colors) ? implode(',', $colors): '',
                        'print' => isset($POST['print']) && $POST['print'] != '' ? $POST['print'] : '',
                        'stock' => isset($POST['stock']) && $POST['stock'] != '' ? $POST['stock'] : '',
                        'coating' => isset($POST['coat']) && $POST['coat'] != '' ? $POST['coat'] : '',
                        'add_ons' => $addon,
                        'currency' => $this->getQuoteCurrency(CURR_EXT),
                        'conversion_rate' => $this->getQuoteConversionRate(CURR_EXT),
                        'ip' => request()->ip(),
                        'utm_source' => (!empty($utm_source) ? $utm_source : 'organic'),
                    ];
                    // echo '<pre>', print_r($subArr);die;
                    $oWebSubQuotations = new \App\Models\WebSubQuotations();
                    $oWebSubQuotations->insert($subArr);
                }
                $quantity3 = $this->checkNumberLength($POST['q3']);
                if ($this->check3rdQuantity($quantity1,$quantity2,$quantity3) == true) {
                    $subArr = [
                        'user_id' => trim($userId),
                        'quote_id' => $lastInsertedId,
                        'quote_number' => $quote_number,
                        'unit' => isset($POST['unit']) && $POST['unit'] != '' ? strtolower($POST['unit']) : '',
                        'length' => isset($POST['length']) && $POST['length'] != '' ? trim($POST['length']) : 0,
                        'width' => isset($POST['width']) && $POST['width'] != '' ? trim($POST['width']) : 0,
                        'height' => isset($POST['height']) && $POST['height'] != '' ? trim($POST['height']) : 0,
                        'type' => isset($POST['product_style']) && $POST['product_style'] != '' ? $POST['product_style'] : '',
                        'quantity' => $quantity3,
                        'color' => isset($colors) && is_array($colors) ? implode(',', $colors): '',
                        'print' => isset($POST['print']) && $POST['print'] != '' ? $POST['print'] : '',
                        'stock' => isset($POST['stock']) && $POST['stock'] != '' ? $POST['stock'] : '',
                        'coating' => isset($POST['coat']) && $POST['coat'] != '' ? $POST['coat'] : '',
                        'add_ons' => $addon,
                        'currency' => $this->getQuoteCurrency(CURR_EXT),
                        'conversion_rate' => $this->getQuoteConversionRate(CURR_EXT),
                        'ip' => request()->ip(),
                        'utm_source' => (!empty($utm_source) ? $utm_source : 'organic'),
                    ];
                    // echo '<pre>', print_r($subArr);die;
                    $oWebSubQuotations = new \App\Models\WebSubQuotations();
                    $oWebSubQuotations->insert($subArr);
                }
                $this->insertIntoQuotationsEstimation($lastInsertedId,CURR_EXT);
                $oProductsList = new \App\Models\ProductsList();
                $productExist = $oProductsList->findByPK($POST['product_id']);
                $addons = [];
                if (isset($POST['addon']) && count($POST['addon']) > 0) {
                    foreach ($POST['addon'] as $addon) {
                        $addons[] = $oIndustryList->getIndustryName($addon);
                    }
                }
                $colorNames = [];
                if(is_array($colors) && count($colors) > 0){
                    foreach ($colors as $color) {
                        $colorNames[] = $oIndustryList->getIndustryName($color);
                    }
                }
                // $base = '';
                // $thickness = '';
                // if ($POST['stock'] == 30 && isset($POST['base']) && $POST['base'] != '') {
                //     $base = $oIndustryList->getIndustryName($POST['base']);
                // } else if ($POST['stock'] != 30 && isset($POST['thickness']) && $POST['thickness'] != '') {
                //     $thickness = $oIndustryList->getIndustryName($POST['thickness']);
                // }
                $print = '';
                if (isset($POST['print']) && $POST['print'] != '') {
                    $print = $oIndustryList->getIndustryName($POST['print']);
                }
                $stock = '';
                if (isset($POST['stock']) && $POST['stock'] != '') {
                    $stock = $oIndustryList->getIndustryName($POST['stock']);
                }
                $coat = '';
                if (isset($POST['coat']) && $POST['coat'] != '') {
                    $coat = $oIndustryList->getIndustryName($POST['coat']);
                }
                $length = isset($POST['length']) && $POST['length'] != '' ? $POST['length'] : 0;
                $width = isset($POST['width']) && $POST['width'] != '' ? $POST['width'] : 0;
                $height = isset($POST['height']) && $POST['height'] != '' ? $POST['height'] : 0;
                $unit = isset($POST['unit']) && $POST['unit'] != '' ? strtolower($POST['unit']) : 'in';
                $emailData = array(
                    'id' => $lastInsertedId,
                    'comments' => trim($POST['comments']),
                    'senderEmail' => strtolower($POST['email']),
                    'contact_no' => isset($POST['contact_no']) && $POST['contact_no'] != '' ? $POST['contact_no'] : '',
                    'referrer' => $POST['referrer'],
                    'fullName' => isset($POST['full_name']) && $POST['full_name'] != '' ? ucwords($POST['full_name']) : '',
                    'name' => isset($productExist->name) && $productExist->name != '' ? $productExist->name : 'Custom Quote',
                    'size' => trim($length) . ' x ' . trim($width) . ' x ' . trim($height) . '(' . trim($unit) . ')',
                    'color' => implode(' + ', $colorNames),
                    'print' => $print,
                    'stock' => $stock,
                    'coating' => $coat,
                    'source' => CURR_EXT != '' ? CURR_EXT : 'COM',
                    'web_contact' => CONTACT_NO != '' ? CONTACT_NO : '1-800-205-9972',
                    'currency' => CURR_EXT != '' && CURR_EXT == 'CO.UK' ? '£' : '$',
                    'date' => CURR_EXT != '' && CURR_EXT == 'COM' ? date('m/d/Y') : date('d/m/Y'),
                    'file' => isset($POST['frmImage']) && $POST['frmImage'] != '' ? $fileName : '',
                    'addons' => implode('<br>', $addons),
                    'quantity1' => isset($POST['q1']) && $POST['q1'] != '' ? $POST['q1'] : '',
                    'quantity2' => isset($POST['q2']) && $POST['q2'] != '' ? $POST['q2'] : '',
                    'quantity3' => isset($POST['q3']) && $POST['q3'] != '' ? $POST['q3'] : '',
                );
                // echo '<pre>', print_r($emailData);die;
                $this->sendEmail([
                    'email' => QUOTES_EMAIL,
                    'subject' => 'CustomBoxline Price Quote #QTE ' . $lastInsertedId . '!',
                    'view' => 'custom_email',
                    'data' => $emailData,
                    'senderName' => 'CustomBoxline',
                ]);
                $response = ['code' => 11, "message" => "Your request has been successfully submitted. "];
            
        } else {
            $response = ['code' => 22, "message" => "Invalid Request."];
        }
        return $this->jsonResponse($response);
    }
}