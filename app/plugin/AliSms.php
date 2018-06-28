<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\plugin;

use library\Curl;

/**
 * Description of AliSms
 *
 * @author meizhao
 */
class AliSms {
    private $accessKeyId = "LTAIrP2JZIJWXiQe";
    private $accessSecret = "kEL6DdcDUemWXdvjWipxUX4uw9Jz5B";
    private $SignName = "星态坊";
    private $dateTimeFormat = 'Y-m-d\TH:i:s\Z';
    private $domainParameters = array();
    private $domain = "http://dysmsapi.aliyuncs.com";
    private $error = "";


    public function __construct() {
       
    }
    
    /**
     * 
     * @param type $PhoneNumbers
     * @param type $TemplateCode
     * @param type $params
     * @return int
     */
    
    public function send($PhoneNumbers,$TemplateCode,$params){
        if(empty($this ->accessKeyId) || empty($this ->accessSecret)){
            $this->error = '配制参数为空';
            return -1;
        }
        //验证码类 $params传 验证码字符
        //
        //SMS_101805002 新用户注册
        //SMS_101735009 重置密码
        //SMS_129742722 绑定提款资料
        //SMS_129762700 转账操作
        //SMS_129742725 挂单售出
        //SMS_129742726 确认交易
        if (in_array($TemplateCode, ['SMS_101805002','SMS_101735009','SMS_129742722','SMS_129762700','SMS_129742725','SMS_129742726'])){
            $params = ['code' => (string)$params];
        }
        //提醒类 $params传数组
        //SMS_129747651 订单付款提示 ['code'=>订单编号]
        //SMS_129747653 买方付款通知 ['code'=>订单编号,'num'=>付款金额]
        elseif (in_array($TemplateCode, ['SMS_129747651','SMS_129747653'])){
            
        }else{
            $this->error = '没有找到模板';
            return -2;
        }
        
        date_default_timezone_set("GMT");
        $data["SignatureMethod"] = "HMAC-SHA1";
        $data["SignatureNonce"] = uniqid();
        $data["AccessKeyId"] = $this ->accessKeyId;
        $data["SignatureVersion"] = "1.0";
        $data["Timestamp"] = date($this->dateTimeFormat);
        $data["Format"] = "XML";

        $data["Action"] = "SendSms";
        $data["Version"] = "2017-05-25";
        $data["RegionId"] = "cn-hangzhou";
        $data["PhoneNumbers"] = $PhoneNumbers;
        $data["SignName"] = $this -> SignName;
        $data["TemplateParam"] = json_encode($params);//{\"customer\":\"test\"}
        $data["TemplateCode"] = $TemplateCode;//SMS_71390007
        $data["Signature"] = $this->computeSignature($data, $this ->accessSecret);
        $requestUrl =  $this -> domain . "/?";

        foreach ($data as $apiParamKey => $apiParamValue)
        {
            $requestUrl .= "$apiParamKey=" . urlencode($apiParamValue) . "&";
        }
        $requestUrl =  substr($requestUrl, 0, -1);
        $request = new Curl();
        $response = @$request->get($requestUrl);
        $array = (array)simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);
        //var_dump($array);
        if($array && isset($array['Code']))
        {
            if($array['Code'] == 'OK'){
                return 0;
            }else{
                $this->error = isset($array['Message']) ? $array['Message'] :'网络出错';
                return -3;
            }


        }
        else
        {
            $this->error = '网络出错';
            return -4;
        }
    }
    
    private function computeSignature($parameters, $accessKeySecret)
    {
        ksort($parameters);
        $canonicalizedQueryString = '';
        foreach($parameters as $key => $value)
        {
            $canonicalizedQueryString .= '&' . $this->percentEncode($key). '=' . $this->percentEncode($value);
        }
        $stringToSign = 'GET&%2F&' . $this->percentencode(substr($canonicalizedQueryString, 1));
        $signature = $this->signString($stringToSign, $accessKeySecret."&");

        return $signature;
    }
    private  function signString($source,$accessSecret){
        return base64_encode(hash_hmac('sha1', $source, $accessSecret, true));
    }
    protected function percentEncode($str)
    {
        $res = urlencode($str);
        $res = preg_replace('/\+/', '%20', $res);
        $res = preg_replace('/\*/', '%2A', $res);
        $res = preg_replace('/%7E/', '~', $res);
        return $res;
    }

    public function getDomainParameter()
    {
        return $this->domainParameters;
    }

    public function putDomainParameters($name, $value)
    {
        $this->domainParameters[$name] = $value;
    }
    public function getError(){
        return $this->error;
    }
}
