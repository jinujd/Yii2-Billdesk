<?php

namespace common\components;
use Yii;
use yii\base\Component;
use yii\helpers\Url;


class BillDeskPayment extends Component {

    public $PAYMENT_URL =  'https://pgi.billdesk.com/pgidsk/PGIMerchantPayment';    
    public $MERCHANT_ID,$CHECKSUM_KEY,$SECRET_KEY,$CURRENCY_TYPE,$REDIRECT_ACTION;    
    
    /*
     * to control the iniatial data
     */
    public function __construct($config=[]) {
     parent::__construct($config);
     $this->MERCHANT_ID =  $this->MERCHANT_ID;
     $this->CHECKSUM_KEY =  $this->CHECKSUM_KEY;
     $this->SECRET_KEY =  $this->SECRET_KEY;
     $this->CURRENCY_TYPE =  $this->CURRENCY_TYPE;
    
     $this->REDIRECT_ACTION = Url::to($this->REDIRECT_ACTION,true);
   }
   
   /*
    * To intitate the payment
    * amount and other additional params as array (amount is mandatory)
    * amount is number type, params is array
    */
   public function initiatePayment($amount, $options=[]) {
     $params['merchantId'] = $this->MERCHANT_ID;
     $params['transaction_id'] = $this->generateRandomString();
     $params['naData1'] = (isset($options['naData1'])) ? $options['naData1'] : 'NA';
     $params['amount'] = ($amount) ? $amount : '';
     $params['naData2'] = (isset($options['naData2'])) ? $options['naData2'] : 'NA';
     $params['naData3'] = (isset($options['naData3'])) ? $options['naData3'] : 'NA';
     $params['naData4'] = (isset($options['naData4'])) ? $options['naData4'] : 'NA';
     $params['currencyType'] = $this->CURRENCY_TYPE;
     $params['naData5'] = (isset($options['naData5'])) ? $options['naData5'] : 'NA';
     $params['typeField'] = 'R';
     $params['securityKey'] =  $this->SECRET_KEY;
     $params['naData7'] = (isset($options['naData7'])) ? $options['naData7'] : 'NA';
     $params['naData8'] = (isset($options['naData8'])) ? $options['naData8'] : 'NA';
     $params['typeField1'] = 'F';
     $params['additionalInfo1'] = (isset($options['additionalInfo1'])) ? $options['additionalInfo1'] : 'NA';
     $params['additionalInfo2'] = (isset($options['additionalInfo2'])) ? $options['additionalInfo2'] : 'NA';
     $params['additionalInfo3'] = (isset($options['additionalInfo3'])) ? $options['additionalInfo3'] : 'NA';
     $params['additionalInfo4'] = (isset($options['additionalInfo4'])) ? $options['additionalInfo4'] : 'NA';
     $params['additionalInfo5'] = (isset($options['additionalInfo5'])) ? $options['additionalInfo5'] : 'NA';
     $params['additionalInfo6'] = (isset($options['additionalInfo6'])) ? $options['additionalInfo6'] : 'NA';
     $params['additionalInfo7'] = (isset($options['additionalInfo7'])) ? $options['additionalInfo7'] : 'NA';
     $params['redirectAction'] = $this->REDIRECT_ACTION;
     
     $convertedData = implode('|',$params);
     $encryptedData = $this->encryptString($convertedData);
     $params['checksumValue'] = $encryptedData;
     $msg = implode('|',$params);
     $data = ['msg'=>$msg];
     $this->curlPost($this->PAYMENT_URL,$data);
   }

    /*
     * To execute the given url with params as post data
     */
   public function curlPost($url, $params=false){     
       $html = "<form method = 'post' action = '$url' id = 'frm1'>";
       foreach($params as $param => $vl) {
           $html .= "<input type = 'hidden' name ='$param' value ='$vl' />";
       }
       $html .= "</form>
      <script>
        document.getElementById('frm1').submit();
      </script>
     ";
       $fields = [];
       echo $html;exit;
       
       
       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL,$url);
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
       curl_setopt($ch, CURLOPT_VERBOSE, true);
       curl_setopt($ch, CURLOPT_POST, 1);
       curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($params));
       curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
       $response = curl_exec ($ch);
       if (curl_errno($ch)) {
           $error_msg = curl_error($ch);
           //echo 'error-'.json_encode($error_msg);exit;
       }
       
       curl_close ($ch);
   }
    
   /*
    * To generate random string
    */
   public function generateRandomString($length = 8) {
       $characters = '0123456789ABCDEFGHIJKLM';
       $charactersLength = strlen($characters);
       $randomString = '';
       for ($i = 0; $i < $length; $i++) {
           $randomString .= $characters[rand(0, $charactersLength - 1)];
       }
       return $randomString.time();
    }
    
    /*
     * to encrypt the given string with sha256 method
     */
    public function encryptString($str){
        return strtoupper(hash_hmac('sha256',$str,$this->CHECKSUM_KEY, false));
    }
    
    /*
     * To check the transaction process is done or not after the transaction
     */
    public function extractParams($msg=false){ 
        $result = false;
        if($msg){
            $code = substr(strrchr($msg, "|"), 1); //Last check sum value
            $newStr =str_replace("|".$code,"",$msg);
            $splitdata = strtoupper(hash_hmac('sha256',$newStr,$this->CHECKSUM_KEY, false));
            
            return $splitdata;
           /* if($splitdata==$code && isset($splitdata[14]) && $splitdata[14] == "0300"){
                $result = true;
            }else{
                $result = false;
            }*/
        }
        return $result;        
    }
    
}

?>