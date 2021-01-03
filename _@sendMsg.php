<?php

//Bot written by Jamron (From FlixTV)

$tgfdurl = 'https://api.telegram.org/bot'.$bot_token.'/sendMessage?chat_id='.$chat['id'].'&text='.htmlentities(urlencode($send_txt)).'&parse_mode=markdown';

$process = curl_init($tgfdurl);
curl_setopt($process, CURLOPT_HEADER, 0);
curl_setopt($process, CURLOPT_USERAGENT, '');
curl_setopt($process, CURLOPT_ENCODING, '');
curl_setopt($process, CURLOPT_TIMEOUT, 30);
curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);

$return = curl_exec($process);
curl_close($process);

$ee11 = json_decode($return, true);

if($ee11['ok'] == true)
{
    $sent_msg_id = $ee11['result']['message_id'];
}

?>
