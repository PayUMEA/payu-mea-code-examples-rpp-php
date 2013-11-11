<?php
    

//-------------------------------------------------------------------
//-------------------------------------------------------------------
//-------
//-------      Configs comes here
//-------
//-------------------------------------------------------------------
//-------------------------------------------------------------------
$baseUrl = 'https://staging.payu.co.za';
$soapWdslUrl = $baseUrl.'/service/PayUAPI?wsdl';
$payuRppUrl = $baseUrl.'/rpp.do?PayUReference=';
$apiVersion = 'ONE_ZERO';

/*
Using staging integartion store 1 details
Store ID: 100284
Webservice username : Staging Integration Store 1
Webservice password : 78cXrW1W
Safekey: {45D5C765-16D2-45A4-8C41-8D6F84042F8C} 
 * 
 * Webservice username : Staging Enterprise Integration Store 1
Webservice password : j3w8swi5
Safekey: {E7A333D4-CC48-4463-BEC6-A4BC1F16DC30}
*/
$safeKey = '{E7A333D4-CC48-4463-BEC6-A4BC1F16DC30}';
$soapUsername = 'Staging Enterprise Integration Store 1';
$soapPassword = 'j3w8swi5';
$merchantReference = '10330456340';
$merchantReference = "merchant_ref_".time();;


$doTransactionArray = array();
$doTransactionArray['Api'] = $apiVersion;
$doTransactionArray['Safekey'] = $safeKey;
$doTransactionArray['TransactionType'] = 'FINALIZE';		

$doTransactionArray['AdditionalInformation']['merchantReference'] = $merchantReference;
//$doTransactionArray['AdditionalInformation']['demoMode'] = 'true'	;
//$doTransactionArray['AdditionalInformation']['notificationUrl'] = ''	;
	
	
$doTransactionArray['Basket']['description'] = "Product Description";
$doTransactionArray['Basket']['amountInCents'] = "10000";
$doTransactionArray['Basket']['currencyCode'] = 'ZAR';


$doTransactionArray['Customer']['merchantUserId'] = "7";
$doTransactionArray['Customer']['email'] = "john@doe.com";
$doTransactionArray['Customer']['firstName'] = 'John';
$doTransactionArray['Customer']['lastName'] = 'Doe';
$doTransactionArray['Customer']['mobile'] = '0211234567';
$doTransactionArray['Customer']['regionalId'] = '1234512345122';
$doTransactionArray['Customer']['countryCode'] = '27';

/*
$doTransactionArray['Creditcard']['nameOnCard'] = "Mr John Doe";
$doTransactionArray['Creditcard']['cardNumber'] = "4242424242424242";
$doTransactionArray['Creditcard']['cardExpiry'] = "042014";
$doTransactionArray['Creditcard']['cvv'] = "123";  
$doTransactionArray['Creditcard']['amountInCents'] = $doTransactionArray['Basket']['amountInCents']; 
 */

//$doTransactionArray['PayUReference'] = 10593955323;


try {

    // 1. Building the Soap array  of data to send - This will make it into the xml as described in the documentation
    $soapDataArray = array();
    $soapDataArray['Api'] = $apiVersion;
    $soapDataArray['Safekey'] = $safeKey;
    $soapDataArray['PayUReference'] = 10595026304;
    $soapDataArray = array_merge($soapDataArray, $doTransactionArray );
    
    
    
    // 2. Creating a XML header for sending in the soap heaeder (creating it raw a.k.a xml mode)
    $headerXml = '<wsse:Security SOAP-ENV:mustUnderstand="1" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">';
    $headerXml .= '<wsse:UsernameToken wsu:Id="UsernameToken-9" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">';
    $headerXml .= '<wsse:Username>'.$soapUsername.'</wsse:Username>';
    $headerXml .= '<wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">'.$soapPassword.'</wsse:Password>';
    $headerXml .= '</wsse:UsernameToken>';
    $headerXml .= '</wsse:Security>';
    $headerbody = new SoapVar($headerXml, XSD_ANYXML, null, null, null);

    // 3. Create Soap Header.        
    $ns = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd'; //Namespace of the WS. 
    $header = new SOAPHeader($ns, 'Security', $headerbody, true);        

    // 4. Make new instance of the PHP Soap client
    $soap_client = new SoapClient($soapWdslUrl, array("trace" => 1, "exception" => 0)); 

    // 5. Set the Headers of soap client. 
    $soap_client->__setSoapHeaders($header); 

    // 6. Do the setTransaction soap call to PayU
    $soapCallResult = $soap_client->doTransaction($soapDataArray); 

    // 7. Decode the Soap Call Result
    $returnData = json_decode(json_encode($soapCallResult),true);
    
    $decodedXmlData = json_decode(json_encode((array) simplexml_load_string($returnData)),true);    
    
    print "<pre>";
    var_dump($decodedXmlData);
    print "</pre>";  

}
catch(Exception $e) {
    var_dump($e);
}

//-------------------------------------------------------------------
//-------------------------------------------------------------------
//-------
//-------      Checking response
//-------
//-------------------------------------------------------------------
//-------------------------------------------------------------------
if(is_object($soap_client)) {    
    //print "Request Header:"; 
    //echo str_replace( '&gt;&lt;' , '&gt;<br />&lt;', htmlspecialchars( $httpClient->getLastRequestHeaders(), ENT_QUOTES)); 
    print "-----------------------------------------------\r\n";
    print "Request:\r\n";
    print "-----------------------------------------------\r\n";            
    $requestString = str_replace( '&gt' , '>', $soap_client->__getLastRequest() );    
    $requestString = str_replace( '&gt' , '>', $requestString );    
    $requestString = str_replace( '>' , ">\r\n", $requestString );
    $requestString = str_replace( '</' , "\r\n</", $requestString );
    $requestString = str_replace( "\r\n\r\n" , "\r\n", $requestString );
    $requestString = str_replace( "\r\n\r\n" , "\r\n", $requestString );
    print $requestString;            
    print "\r\n";
    print "-----------------------------------------------\r\n";
    print "Response:\r\n";
    print "-----------------------------------------------\r\n";
    $responseString = str_replace( '&gt' , '>', $soap_client->__getLastResponse() );    
    $responseString = str_replace( '&gt' , '>', $responseString );    
    $responseString = str_replace( '>' , ">\r\n", $responseString );
    $responseString = str_replace( '</' , "\r\n</", $responseString );
    $responseString = str_replace( "\r\n\r\n" , "\r\n", $responseString );    
    print $responseString;            
} 