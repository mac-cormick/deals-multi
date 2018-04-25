<?php

ini_set('max_execution_time', 1800);

// Авторизация
$user = array(
    'USER_LOGIN'=>'amolyakov@team.amocrm.com',
    'USER_HASH'=>'691c2c8c35794e95be679e7a21d40c40'
);
$subdomain = 'newdemonew';
$auth = 'https://'.$subdomain.'.amocrm.ru/private/api/auth.php?type=json';
$ch = curl_init();
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_USERAGENT,'amoCRM-API-client/2.0');
curl_setopt($ch,CURLOPT_URL,$auth);
curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'POST');
curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($user));
curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
curl_setopt($ch,CURLOPT_HEADER,false);
curl_setopt($ch,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt');
curl_setopt($ch,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt');
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
$out = curl_exec($ch);
curl_close($ch);

$Response=json_decode($out,true);
$Response=$Response['response'];

if (isset($Response['auth']))
{
    echo 'Авторизация прошла успешно<br>';
} else {
    echo 'Ошибка авторизации<br>';
}

$custom_field_id = 561921;
$leads_result = true;
$i = 0;

while ($leads_result) {
    sleep(1);
    $limit_offset = $i*500;
    $i++;
    echo $i.'<br>';

    // Получение списка сделок по 500

    $link = 'https://'.$subdomain.'.amocrm.ru/api/v2/leads?status=143&limit_rows=500&limit_offset='.$limit_offset;
    echo $link.'<br>';
    $headers[] = "Accept: application/json";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl, CURLOPT_USERAGENT, "amoCRM-API-client/2.0");
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_URL, $link);
    curl_setopt($curl, CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__)."/cookie.txt");
    curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__)."/cookie.txt");
    $out = curl_exec($curl);
    curl_close($curl);
    $leads_result = json_decode($out,TRUE);

    // echo '<pre>';
    // var_dump($leads_result);
    // echo '</pre>';

    if (!$leads_result) {
        break;
    }

    $leads = $leads_result['_embedded']['items']; // Массив сделок

   foreach ($leads as $lead) {
       $custom_fields = $lead['custom_fields'];
       if (count($custom_fields) > 0) {
           foreach ($custom_fields as $custom_field) {
                echo '<pre>';
                var_dump($custom_field);
                echo '</pre>';
               if ($custom_field['id'] == $custom_field_id) {
                   $custom_field_value = $custom_field['values']['value'];
                   echo $custom_field_value.'<br>';
               }
           }
       }
   }
}
