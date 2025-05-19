<?php

namespace App\Traits;

trait CommonFunction
{

    private $enLabels = array(
        'Note:',
        'Microsoft Internet Explorer restricts the images to be uploaded one by one. To upload multiple images at the same time please use other browser.',
        'Please enable JavaScript to use file uploader.'
    );
    private $allowedExtensions = array('jpg', 'jpeg', 'gif', 'png', 'txt', 'xls', 'xlsx', 'pdf', 'docx', 'doc', 'csv');
    private $sizeLimit = 1048576; //1 * 1024r * 1024; // max file size in bytes

    public function obtainPost()
    {
        return request()->input();
    }

    public function obtainGet()
    {
        return request()->input();
    }

    public function obtainRequest()
    {
        return request()->input();
    }

    public function isPost()
    {
        return request()->isMethod('post');
    }

    public function isGet()
    {
        return request()->isMethod('get');
    }

    public function setState($key, $value)
    {
        session()->put($key, $value);
        session()->save();
    }

    public function getState($key)
    {
        return session()->get($key);
    }

    public function unsetState($key)
    {
        return session()->forget($key);
    }

    public function flushState()
    {
        return request()->session()->flush();
        session()->save();
    }

    public function input($key)
    {
        return request()->get($key);
    }

    public function getQueryBuilder($table)
    {
        if (!empty($table)) {
            return \Illuminate\Support\Facades\DB::table($table);
        }
        return false;
    }

    public function isGuest()
    {
        if (!empty($this->getState('user'))) {
            return false;
        }
        return true;
    }

    public function getControllerNameSpace()
    {
        return trim(request()->route()->getPrefix(), '/');
    }

    public function getViewFolder()
    {
        return str_replace("/", ".", $this->getControllerNameSpace());
    }

    public function getActionName()
    {
        $requestArr = explode("\\", request()->route()->getAction()['controller']);
        $controllerAction = $requestArr[key(array_slice($requestArr, -1, 1, true))];
        $controllerActionArr = explode("@", $controllerAction);
        return str_replace("Controller", "", $controllerActionArr[1]);
    }

    public function getControllerName()
    {
        $requestArr = explode("\\", request()->route()->getAction()['controller']);
        $controllerAction = $requestArr[key(array_slice($requestArr, -1, 1, true))];
        $controllerActionArr = explode("@", $controllerAction);
        return str_replace("Controller", "", $controllerActionArr[0]);
    }

    public function adminLayout()
    {
        $baseFolder = $this->getViewFolder();
        if($baseFolder != 'admin'){
            $mainLayout = "\layout\main";
        }else{
            $mainLayout = "\layout\main";
        }
        // $mainLayout = "\layout\main";
        // echo $baseFolder;die;
        return str_replace("\\", ".", $baseFolder . $mainLayout);
    }


    public function renderT($view = null, $data = [], $mergeData = [])
    {
        $baseFolder = $this->getViewFolder();
        $newView = $view;
        $controllerName = strtolower($this->getControllerName());

        $content = view((!empty($baseFolder) ? $baseFolder . "." : '') . $controllerName . "." . $newView, $data, $mergeData);
        return view($this->layout(), $data, $mergeData)->with('content', $content);
    }
    
    public function layout()
    {
        // echo $this->adminLayout();die;
        return trim($this->adminLayout(), '.');
    }

    public function emailLayout()
    {
        $baseFolder = $this->getViewFolder();
        return str_replace("\\", ".", $baseFolder . "emails\layout\main");
        // return str_replace("\\", ".", $baseFolder . "emails\custom_email");
    }

    public function render($view = null, $data = [], $mergeData = [])
    {
        $baseFolder = $this->getViewFolder();
        $controllerName = strtolower($this->getControllerName());
        if ($this->isGuest()) {
            return view('admin.login.' . $view, $data, $mergeData);
        } else {
            $content = view($baseFolder . "." . $controllerName . "." . $view, $data, $mergeData);
            return view($this->adminLayout(), $data, $mergeData)->with('content', $content);
        }
    }

    public function renderCustom($view = null, $data = [], $mergeData = [])
    {
        $baseFolder = $this->getViewFolder();
        $controllerName = strtolower($this->getControllerName());
        if ($this->isGuest()) {
            return view('admin.login.' . $view, $data, $mergeData);
        } else {
            return view($baseFolder . "." . $controllerName . "." . $view, $data, $mergeData);
            // return view($this->adminLayout(), $data, $mergeData)->with('content', $content);
        }
    }

    public function jsonResponse($arr)
    {
        header('Content-Type: application/json');
        return json_encode($arr);
    }

    public function setFlash($key, $value)
    {
        session()->flash($key, $value);
    }

    public function getFlash($key)
    {
        if (session()->has($key)) {
            return session()->get($key);
        }
        return '';
    }

    public function getAjaxUploaderCode($params)
    {
        $lang = (isset($params['lang']) ? $params['lang'] : 'en');
        $labels = ($lang == 'en' ? $this->enLabels : '');
        $element_id = (isset($params['element_id']) ? $params['element_id'] : 'qquploader');
        $maxfilestxt = (isset($params['maxfilestxt']) ? $params['maxfilestxt'] : 'maxFiles');
        $maxfiles = (isset($params['maxfiles']) ? $params['maxfiles'] : '1');
        $showOnLoad = (isset($params['showOnLoad']) ? $params['showOnLoad'] : 'Y');
        $maxfilesize = (isset($params['maxfilesize']) ? $params['maxfilesize'] : $this->sizeLimit);
        $extensions = (isset($params['extensions']) ? $params['extensions'] : 'doc');
        $postURL = (isset($params['postURL']) ? $params['postURL'] : '');
        $fileFieldName = (isset($params['fileFieldName']) ? $params['fileFieldName'] : 'frmImages');
        $droparea = (isset($params['droparea']) != '' ? $params['droparea'] : 'N');
        $callbackFunction = (isset($params['callbackFunction']) ? $params['callbackFunction'] : '');

        $returnStr = '<div id="' . $element_id . '" class="ajxUploaderCnt"><noscript><p>' . $labels[2] .'</p></noscript></div> <div style="display:none;" class="browserCheckUploader"><p><b>' . $labels[0] . '</b>' . $labels[1] . '</p></div><input type="hidden" rel="' . $maxfilestxt . '" name="' . $maxfilestxt . '" value="' . $maxfiles . '" /> <input type="hidden" rel="frmImages" name="' . $fileFieldName . '" value="" />';

        if ($showOnLoad == 'Y') $returnStr .= '<script type="text/javascript">jQuery(document).ready(function($){	createAjaxUploader({eid:\'#' . $element_id . '\',maxfilestxt:\'' . $maxfilestxt . '\',maxfilesize:\'' . $maxfilesize . '\',droparea:\'' . $droparea . '\',exts:\'' . $extensions . '\',URL:\'' . $postURL . '\' ' . ($callbackFunction != '' ? ",callback:" . $callbackFunction : '') . '} );});</script>'; return $returnStr;
    }

    public function uploader()
    {
        return new \App\Uploader\MyUploader();
    }

    /*     * *******************
     * Parameters for send email
     * email
     * subject
     * fromName
     * emailFrom
     * replyTo
     * view
     * cc
     * bcc
     * attachment (storage path of file)
     * ******************************* */

    public function sendEmail($arr)
    {
        if(isset($arr['view']) && $arr['view'] !== ''){
            if($arr['view'] !== 'thank_you' && $arr['view'] !== 'custom_email' && $arr['view'] !== 'error_log_email' && $arr['view'] !== 'custom_quote_email' && $arr['view'] !== 'prototype_email' && $arr['view'] !== 'dieline_email' && $arr['view'] !== 'product.subscriber-for-newsletter' && $arr['view'] !== 'customer_order_email' && $arr['view'] !== 'customer_sample_order_email'){
                $arr['bcc'] = 'bcc@customboxline.com';
            }
        }
        // echo '<pre>',print_r($arr);die;
        // $arr['bcc'] = ['sales@customboxline.com', 'sales@customboxline.com'];
        // $arr['cc'] = ['sales@customboxline.com', 'sales@customboxline.com'];
        // $arr['bcc'] = ['solsquare.inc@gmail.com', 'solsquare.inc@gmail.com'];
        if (!empty($arr['render'])) {
            return (new \App\Mail\SendEmail($arr))->render();
        } else {
            return \Illuminate\Support\Facades\Mail::to($arr['email'])->send(new \App\Mail\SendEmail($arr));
        }
    }
    public function sendNewsLettersEmail($arr)
    {
        // echo '<pre>',print_r($arr);die;
        if (!empty($arr['render'])) {
            return (new \App\Mail\NewsLettersEmail($arr))->render();
        } else {
            // return \Illuminate\Support\Facades\Mail::to($arr['email'])->send(new \App\Mail\NewsLettersEmail($arr));
            return \Illuminate\Support\Facades\Mail::to($arr['email'])->later(now()->addMinutes(2),new \App\Mail\NewsLettersEmail($arr));
        }
    }
    public function sendEmailQuote($arr)
    {
        // if(isset($arr['view']) && $arr['view'] !== ''){
        //     if($arr['view'] !== 'thank_you' && $arr['view'] !== 'custom_email' && $arr['view'] !== 'error_log_email' && $arr['view'] !== 'custom_quote_email' && $arr['view'] !== 'prototype_email' && $arr['view'] !== 'dieline_email' && $arr['view'] !== 'product.subscriber-for-newsletter' && $arr['view'] !== 'customer_order_email' && $arr['view'] !== 'customer_sample_order_email'){
        //         $arr['bcc'] = 'bcc@customboxline.com';
        //     }
        // }
        // echo '<pre>',print_r($arr);die;
        // $arr['bcc'] = ['sales@customboxline.com', 'sales@customboxline.com'];
        // $arr['cc'] = ['sales@customboxline.com', 'sales@customboxline.com'];
        // $arr['bcc'] = ['solsquare.inc@gmail.com', 'solsquare.inc@gmail.com'];
        if (!empty($arr['render'])) {
            return (new \App\Mail\SendEmailQuote($arr))->render();
        } else {
            return \Illuminate\Support\Facades\Mail::to($arr['email'])->send(new \App\Mail\SendEmailQuote($arr));
        }
    }
    public function sendMultiEmail($arr)
    {
        // echo '<pre>',print_r($arr);die;
        // $arr['bcc'] = ['sales@customboxline.com', 'sales@customboxline.com'];
        // $arr['cc'] = ['sales@customboxline.com', 'sales@customboxline.com'];
        // $arr['bcc'] = ['solsquare.inc@gmail.com', 'solsquare.inc@gmail.com'];
        return (new \App\Mail\SendMultiEmail($arr))->render();
    }
    public function SendMultiEmailWorking($arr)
    {
        // $arr['bcc'] = ['sales@customboxline.com', 'sales@customboxline.com'];
        // $arr['cc'] = ['sales@customboxline.com', 'sales@customboxline.com'];
        // $arr['bcc'] = ['solsquare.inc@gmail.com', 'solsquare.inc@gmail.com'];
        return (new \App\Mail\SendMultiEmailWorking($arr))->render();
    }

    public function paginator()
    {
        return new Paginator();
    }

    public function setMeta($arr)
    {
        $this->setFlash('site_meta_arr', $arr);
    }

    public function myArrayUnique($array, $keep_key_assoc = false)
    {
        // echo '<pre>',print_r($array);die;
        $duplicate_keys = array();
        $tmp = array();
        foreach ($array as $key => $val) {
            if (is_object($val)) {
                $val = (array) $val;
            }
            if (!in_array($val, $tmp)) {
                $tmp[] = $val;
            } else {
                $duplicate_keys[] = $key;
            }
        }
        foreach ($duplicate_keys as $key) {
            unset($array[$key]);
        }
        // echo '<pre>',print_r($array);die;
        return $array;
    }

    public function myObjectArrayUniqueProductList($arrayOfObjects, $keep_key_assoc = false)
    {
        // echo '<pre>',print_r($arrayOfObjects);die;
        $duplicate_keys = array();
        $allKeys = array();
        foreach ($arrayOfObjects as $key => $val) {
            if (is_object($val)) {
                $val = (array) $val;
            }
            $allKeys[$val['product_id']] = $val;
            if (!in_array($val, $allKeys)) {
                $tmp[] = $val;
            } else {
                $duplicate_keys[] = $key;
            }
        }
        return $allKeys;
    }

    function limitedText($text, $limit) {
        if (str_word_count($text, 0) > $limit) {
            $words = str_word_count($text, 2);
            $pos   = array_keys($words);
            $text  = substr($text, 0, $pos[$limit]) . '...';
        }
        return $text;
    }

    function unitConversion($length,$width,$height,$unit) {
        $data = [];
        if($unit == 'cm'){
            $data['length'] = round($length * 0.393701,2);
            $data['width'] = round($width * 0.393701,2);
            $data['height'] = round($height * 0.393701,2);
        }else if($unit == 'mm'){
            $data['length'] = round($length * 0.0393701,2);
            $data['width'] = round($width * 0.0393701,2);
            $data['height'] = round($height * 0.0393701,2);
        }else{
            $data['length'] = round($length,2);
            $data['width'] = round($width,2);
            $data['height'] = round($height,2);
        }
        return $data;
    }

    function openSizeFormulations($arr) {
        // echo '<pre>',print_r($size);die;
        $data = [];
        $size = $this->unitConversion($arr['length'],$arr['width'],$arr['height'],$arr['unit']);
        $str1 = $arr["formulaOpenLength"];
        eval("\$str1 = $str1;");
        $str2 = $arr["formulaOpenWidth"];
        eval("\$str2 = $str2;");
        $data['open_length'] = round($str1,2);
        $data['open_width'] = round($str2,2);
        return $data;
    }
    function openSizeFormulationsMulti($arr) {
        // echo '<pre>',print_r($size);die;
        $data = [];
        $size = $this->unitConversion($arr['length'],$arr['width'],$arr['height'],$arr['unit']);
        for ($i=1; $i <= 3; $i++) {
            if($arr["formulaOpenLength_$i"] != '' && $arr["formulaOpenWidth_$i"] != ''){
                $str1 = $arr["formulaOpenLength_$i"];
                eval("\$str1 = $str1;");
                $str2 = $arr["formulaOpenWidth_$i"];
                eval("\$str2 = $str2;");
                $data["open_length$i"] = round($str1,2);
                $data["open_width$i"] = round($str2,2);
            }
        }
        // echo '<pre>',print_r($data);die;
        return $data;
    }
}
