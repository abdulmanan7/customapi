<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Customapi_lib
{
    protected $ci;
    protected $encrypted_key="54%&*$#$";
    protected $bank_sec_key="162902e822c1f27a86e40d01e991893e918b0ae2bb49adcce6f26496829c9dbeba4b0386a3550a8e753a9d07c2e9e2dcecee350106593a5dbdbf88a494f8dd56" ;
    // protected $bank_sec_key="FikC6CLB8nqG5A0B6ZGJPpGLCuK7Sa3M5vJkloKcnb66SwOGo1UKjnU6nQfC6eLc7O41AQZZOl29v4iklPjdVg==" ;
    protected $bank_app_key="eY/etUTuQV9SFC/nTXXT3S6jE8kI6GxDiC9ugjEIjJz9DkhRtPn4cQdJZiElb1Bqv/b32Tj4F8YGpXZwl3q6Jw==" ;
	function __construct()
    {
        // Construct our parent class
        $this->ci = &get_instance();
    }
    public function genarate_key($id='',$app_key=FALSE)
    {
        if ($app_key) {
        return $encrypted = $this->ci->encrypt->encode($id, $this->encrypted_key);
        }
        return $encrypted = $this->ci->encrypt->encode($id, $this->encrypted_key.$id);
    }
    public function validate_user($id,$sec_key)
    {
        $decrypted = $this->ci->encrypt->decode($sec_key, $this->encrypted_key.$id);
        if ($decrypted==$id) {
            return TRUE;
        }
        else{
            return FALSE;
        }

    }
    public function utcTimestamp()
    {
        // date_default_timezone_set("UTC");
        // return date("Y-m-d H:i:s", time()); 
        // $date = new DateTime();
        // return $date->getTimestamp();
        return time();
    }
    public function createHmacsha($str='',$tec='SHA256')
    {
        echo $enc_string = hash_hmac($tec, $str, $this->bank_sec_key);
        $enc_string = hash_hmac($tec, $str, $this->bank_sec_key,true);
        return base64_encode($enc_string);
    }
    
}