<?php
include_once(ROOT_PATH . '/config.php'); 
include_once(ROOT_PATH . '/admin/email/emailLogic.php'); 
getEmailSettings();

$url = 'https://api.sendgrid.com/';
$pass = $apikey;
$from = $email_from;

function sendEmail($to, $subject, $content) {
    global $url, $apikey, $pass, $from;

    $js = array(
        // 'sub' => array(':name' => array('Elmer')),
    );
    
    $params = array(
        'to'        => $to,
        'toname'    => "Example User",
        'from'      => $from,
        'fromname'  => APP_NAME,
        'subject'   => $subject,
        'text'      => $content,
        'html'      => $content,
        // 'x-smtpapi' => json_encode($js),
      );
    
    $request =  $url.'api/mail.send.json';
    
    // Generate curl request
    $session = curl_init($request);
    // Tell PHP not to use SSLv3 (instead opting for TLS)
    curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
    curl_setopt($session, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $apikey));
    // Tell curl to use HTTP POST
    curl_setopt ($session, CURLOPT_POST, true);
    // Tell curl that this is the body of the POST
    curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
    // Tell curl not to return headers, but do return the response
    curl_setopt($session, CURLOPT_HEADER, false);
    curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
    
    // obtain response
    $response = curl_exec($session);
    curl_close($session);
}


?>
