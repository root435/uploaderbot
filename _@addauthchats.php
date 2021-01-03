<?php

//Bot written by Jamron (From FlixTV)

$cdc = explode(' ', $msg['text']);
if(empty($cdc[1]))
{
    //Authorize Current Chat
    $authcd = json_decode(@file_get_contents('_@@_authchats.json'), true);
    $authcayy = array();
    if(empty($authcd))
    {
        $authcayy[] = $chat['id'];
        $authcjson = json_encode($authcayy);
        $svdata = fopen("_@@_authchats.json", "w");
        fwrite($svdata, $authcjson);
        fclose($svdata);
        $authop = 1;
    }
    else
    {
        $authcayy = array();
        foreach($authcd as $authxy)
        {
            if($authxy == $chat['id'])
            {
                $authop = 2;
            }
            else
            {
                $authop = 1;
                $authcayy[] = $authxy;
            }
        }
        $authcayy[] = $chat['id'];
        $authcjson = json_encode($authcayy);
        $svdata = fopen("_@@_authchats.json", "w");
        fwrite($svdata, $authcjson);
        fclose($svdata);
    }
}
else
{
    //Authorize Custom Chat ID
    $authcd = json_decode(@file_get_contents('_@@_authchats.json'), true);
    $authcayy = array();
    if(empty($authcd))
    {
        $authcayy[] = $cdc[1];
        $authcjson = json_encode($authcayy);
        $svdata = fopen("_@@_authchats.json", "w");
        fwrite($svdata, $authcjson);
        fclose($svdata);
        $authop = 1;
    }
    else
    {
        $authcayy = array();
        foreach($authcd as $authxy)
        {
            if($authxy == $cdc[1])
            {
                $authop = 2;
            }
            else
            {
                $authop = 1;
                $authcayy[] = $authxy;
            }
        }
        $authcayy[] = $cdc[1];
        $authcjson = json_encode($authcayy);
        $svdata = fopen("_@@_authchats.json", "w");
        fwrite($svdata, $authcjson);
        fclose($svdata);
    }

}
?>
