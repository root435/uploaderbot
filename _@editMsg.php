<?php

$tgfdurl = 'https://api.telegram.org/bot'.$bot_token.'/editMessageText?chat_id='.$chat['id'].'&message_id='.$sent_msg_id.'&text='.htmlentities(urlencode($send_txt)).'&parse_mode=markdown';

$process = curl_init($tgfdurl);
curl_setopt($process, CURLOPT_HEADER, 0);
curl_setopt($process, CURLOPT_USERAGENT, '');
curl_setopt($process, CURLOPT_ENCODING, '');
curl_setopt($process, CURLOPT_TIMEOUT, 10);
curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);

$return = curl_exec($process);
curl_close($process);

?>
