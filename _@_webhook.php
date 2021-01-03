<?php

//Bot written by Jamron (From FlixTV)

include('_@botconfigs.php');

$is_auth_chat = false;

$tghit = @file_get_contents("php://input");
$hits = json_decode($tghit, true);
$is_replied = $is_reply_forwarded = "";

$msg = array('id' => $hits['message']['message_id'],
             'text' => $hits['message']['text']);

$chat = array('id' => $hits['message']['chat']['id']);

$sent_by = array('id' => $hits['message']['from']['id'],
                 'username' => $hits['message']['from']['username'],
                 'name' => $hits['message']['from']['first_name']);

if(isset($hits['message']['reply_to_message']))
{
    $is_replied = true;
    $replied_msg = array('msg_id' => $hits['message']['reply_to_message']['message_id'],
                         'sender_id' => $hits['message']['reply_to_message']['from']['id'],
                         'sender_username' => $hits['message']['reply_to_message']['from']['username'],
                         'sender_name' => $hits['message']['reply_to_message']['from']['first_name'],
                         'chat_id' => $hits['message']['reply_to_message']['chat']['id'],
                         'chat_name' => $hits['message']['reply_to_message']['chat']['title'],
                         'chat_type' => $hits['message']['reply_to_message']['chat']['type']);

}

$getauthchats = json_decode(@file_get_contents('_@@_authchats.json'), true);
foreach($getauthchats as $tgagcg)
{
    if($chat['id'] == $tgagcg)
    {
        $is_auth_chat = true;
    }
}

if(stripos($msg['text'], $check_command) !== false)
{
    $send_txt = 'Yes, I am listening !';
    include('_@sendMsg.php');
    exit();
}

if($sent_by['id'] == $sudo_owner)
{
    if(stripos($msg['text'], '/start') !== false)
    {
        $send_txt = 'Hello, Owner !';
        include('_@sendMsg.php');
        exit();
    }

    if(stripos($msg['text'], $authorize_command) !== false)
    {
        $authop = 0;
        include('_@addauthchats.php');
        if($authop == 0)
        {
            $send_txt = 'Error: Failed To authorize chat !';
        }
        else
        {
            if($authop == 2)
            {
                $send_txt = 'Exception: Chat already authorized !';
            }
            else
            {
                if($authop == 1)
                {
                    $send_txt = 'Chat authorized successfully !';
                }
            }
        }
        include('_@sendMsg.php');
        exit();
    }


}

if($is_auth_chat == true || $sent_by['id'] == $sudo_owner)
{

    if(stripos($msg['text'], $reset_command) !== false)
    {
    $file1 = @file_get_contents('p1.txt');
    $file2 = @file_get_contents('p2.txt');
    if(!empty($file1))
    {
        @unlink(trim($file1));
    }
    if(!empty($file2))
    {
        @unlink(trim($file2));
    }
    @unlink('p1.txt');
    @unlink('p2.txt');
    @unlink('t1.txt');
    @unlink('t2.txt');
    $send_txt = 'Reset Successful';
    include('_@sendMsg.php');
    exit();
    }

    if(stripos($msg['text'], '/start') !== false)
    {
        $send_txt = 'Heyya, Type /help to learn more !';
        include('_@sendMsg.php');
        exit();
    }

    if(stripos($msg['text'], $disable_cname) !== false)
    {
        unlink('_@@_customfname.txt');
        $send_txt = 'Custom Filename Prefix disabled !';
        include('_@sendMsg.php');
        exit();
    }

    if(stripos($msg['text'], $get_cname) !== false)
    {
        $cfname = @file_get_contents('_@@_customfname.txt');
        if(empty($cfname))
        {
            $send_txt = 'No Custom Filename Prefix is set !';
            include('_@sendMsg.php');
            exit();
        }
        else
        {
            $send_txt = "Current Filename Prefix - *$cfname*";
            include('_@sendMsg.php');
            exit();
        }

    }

    if(stripos($msg['text'], $set_cname) !== false)
    {
        $rdf = explode(' ', $msg['text']);
        if(empty($rdf[1]))
        {
            $send_txt = '*Error: Please Enter Custom Filename Prefix*';
            $send_txt .= "\n";
            $send_txt .= "\n";
            $send_txt .= "*Usage:* /setcname _<customprefix>_";
            include('_@sendMsg.php');
            exit();
        }
        else
        {
            $cfile = fopen("_@@_customfname.txt", "w");
            fwrite($cfile, $rdf[1]);
            fclose($cfile);
            $send_txt = 'Custom Filename Prefix was set !';
            include('_@sendMsg.php');
            exit();
        }

    }

    if(stripos($msg['text'], '/help') !== false)
    {
        $send_txt = '*I upload files at Bayfiles.com*';
        $send_txt .= "\n";
        $send_txt .= '_Supports maximum 2 files at a time_';
        $send_txt .= "\n";
        $send_txt .= "\n";
        $send_txt .= "*$upload_command* _<fileurl>_///_<filename>_ - Upload Files";
        $send_txt .= "\n";
        $send_txt .= "*$reset_command* - Restart Bot";
        $send_txt .= "\n";
        $send_txt .= "*$check_command* - Check if bot is alive or fucked up !";
        $send_txt .= "\n";
        $send_txt .= "*$set_cname* - Set Custom Prefix for Filename !";
        $send_txt .= "\n";
        $send_txt .= "*$get_cname* - Get current Custom Prefix for Filename !";
        $send_txt .= "\n";
        $send_txt .= "*$disable_cname* - Remove Custom Prefix for Filename !";
        $send_txt .= "\n";
        $send_txt .= '*/authorize* - Authorizes Current Chat To Use This Bot';
        $send_txt .= "\n";
        $send_txt .= '*/authorize* _<chatid>_ - Authorizes Other Chat To Use This Bot';
        include('_@sendMsg.php');
        exit();
    }

    if(stripos($msg['text'], $upload_command) !== false)
    {
        if(!file_exists('t1.txt') && !file_exists('t2.txt'))
        {
            include('_@processfile1.php');
            exit();
        }

        if(file_exists('t1.txt') && !file_exists('t2.txt'))
        {
            include('_@processfile2.php');
            exit();
        }

        if(!file_exists('t1.txt') && file_exists('t2.txt'))
        {
            include('_@processfile1.php');
            exit();
        }

        if(file_exists('t1.txt') && file_exists('t2.txt'))
        {
            die();
            exit();
        }
    }

}

?>
