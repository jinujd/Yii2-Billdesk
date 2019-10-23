# Yii2-Billdesk
Billdesk Payment Gateway Integration for PHP Yii2.0 Framework

How to configure
----------------
1. Put the CCAvenueComponent.php file to``` /common/components```.
2. Add the component in ```main.php```. Sample code in main.php is given below
```
<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'timeZone' => 'Asia/Kolkata',
    'components' => [ 

     'billDeskPayment' => [
        'class' => 'common\components\BillDeskPayment',
        'MERCHANT_ID' => '<YOUR MERCHANT ID>',
        'SECRET_KEY' => '<YOUR SECRET KEY>',
        'CHECKSUM_KEY' => '<YOUR CHECKSUM KEY>',
		'CURRENCY_TYPE' => '<YOUR CURRENCY TYPE>',
        'REDIRECT_ACTION' => <ACTION TO HANDLE PAYMENT SUCCESS/FAILURE>, // ex: ['site/after-payment']
     ],  
];
?>
```

In the above code , configure the redirect and cancel actions appropriately.
In the redirect action or cancel action, you can extract the received parameters using 
```
$params = Yii::$app->billDeskPayment->extractParams();
```
In $params order status and mechant parameters will be available. 





