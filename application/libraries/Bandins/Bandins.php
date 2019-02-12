<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use phpseclib\Crypt\AES;

class Bandins
{
    private $appid = '';
    private $header = [
        'x-appid' => '',
        'Accept' => 'application/json'
    ];
    private $aes = null;
    private $apis = [
        'token' => 'http://apis.bandins.com/vi/token?securitySign=%s',
        'policies' => 'http://apis.bandins.com/vi/policies?securitySign=%s',
    ];

    public function __construct(array $config = [])
    {
        $this->appid = $config['appid'];
        $this->header['x-appid'] = $config['appid'];

        $this->aes = new AES(AES::MODE_ECB);
        $this->aes->setKeyLength(256);
        $this->aes->setKey(base64_decode($config['appSecrectKey']));
    }

    /**
     * 调用接口
     * @param $action
     * @param array $querys
     * @param array $bodys
     * @return string
     */
    public function exec($action, array $querys = [], array $bodys = [], $method = 'get') : string {
        $sMsgEncrypt = base64_encode($this->aes->encrypt(base64_encode(json_encode($bodys))));
        $securitySign = $this->genarateSinature($querys, $sMsgEncrypt);
        $url = sprintf($this->apis[$action], $securitySign);
        $url .= empty($querys) ? '' : '&' . http_build_query($querys);

        $options = [];
        if($method == 'post'){
            $request = \Requests::post(
                $url,
                $this->header,
                $sMsgEncrypt,
                $options
            );
        }else{
            $request = \Requests::get(
                $url,
                $this->header,
                $options
            );
        }

        if(!is_null(json_decode($request->body))){
            return $request->body;
        }
        return $this->aes->decrypt(base64_decode($request->body));
    }

    /**
     * 生成签名
     * @param array $queryParamsValues
     * @param $sMsgEncrypt
     * @return string
     */
    private function genarateSinature(array $queryParamsValues, $sMsgEncrypt) : string
    {
        $_params = '';
        if(is_array($queryParamsValues) && count($queryParamsValues)){
            $_params = [];
            foreach ($queryParamsValues as $key => $val) {
                $_params[] = $key . $val;
            }
            sort($_params);
            print_r($_params);
            $_params = implode('', $_params);
        }
        return md5($this->appid . $_params . $sMsgEncrypt);
    }
}
