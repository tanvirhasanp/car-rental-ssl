<?php
class SSLCommerz {
    private $store_id;
    private $store_password;
    private $session_api;
    private $validation_api;
    
    public function __construct($store_id, $store_password) {
        $this->store_id = $store_id;
        $this->store_password = $store_password;
        $this->session_api = SSLCZ_SESSION_API;
        $this->validation_api = SSLCZ_VALIDATION_API;
    }
    
    public function makePayment($post_data) {
        $post_data['store_id'] = $this->store_id;
        $post_data['store_passwd'] = $this->store_password;
        
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $this->session_api);
        curl_setopt($handle, CURLOPT_TIMEOUT, 30);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($handle, CURLOPT_POST, 1);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE);
        
        $content = curl_exec($handle);
        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        
        if($code == 200 && !curl_errno($handle)) {
            curl_close($handle);
            $sslcommerzResponse = $content;
        } else {
            curl_close($handle);
            $sslcommerzResponse = false;
        }
        
        $sslcz = json_decode($sslcommerzResponse, true);
        
        if(isset($sslcz['GatewayPageURL']) && $sslcz['GatewayPageURL'] != "") {
            return array(
                'status' => 'SUCCESS',
                'data' => $sslcz['GatewayPageURL'],
                'logo' => $sslcz['storeLogo']
            );
        } else {
            return array(
                'status' => 'FAILED',
                'data' => $sslcz,
                'message' => 'Session was not created successfully.'
            );
        }
    }
    
    public function orderValidation($val_id, $store_id, $store_password, $order_id) {
        $validation_data = array(
            'val_id' => $val_id,
            'store_id' => $store_id,
            'store_passwd' => $store_password,
            'v' => 1,
            'format' => 'json'
        );
        
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $this->validation_api);
        curl_setopt($handle, CURLOPT_TIMEOUT, 30);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($handle, CURLOPT_POST, 1);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $validation_data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE);
        
        $content = curl_exec($handle);
        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        
        if($code == 200 && !curl_errno($handle)) {
            curl_close($handle);
            $response = $content;
        } else {
            curl_close($handle);
            $response = false;
        }
        
        return json_decode($response, true);
    }
}
?>
