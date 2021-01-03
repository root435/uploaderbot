<?php

set_time_limit(0);

if(file_exists('t2.txt'))
{
    $ttdata = @file_get_contents('t2.txt');
    if(trim($ttdata) == trim(base64_encode($msg['text'])))
    {
        die();
        exit();
    }
}

if(file_exists('t1.txt'))
{
    die();
    exit();
}

$geturl = explode(' ', $msg['text']);
if(empty($geturl[1]))
{
    $send_txt = '*Error: Please Provide Direct URL To File*';
    $send_txt .= "\n";
    $send_txt .= "\n";
    $send_txt .= "*Usage:* $upload_command _<url>_///_<newfilename>_";
    include('_@sendMsg.php');
    exit();
}
else
{
    $file_url = $geturl[1]; //File URL
    $getfname = explode('///', $file_url);
    if(!empty($getfname[1]))
    {
        $filename = $getfname[1];
        $file_url = $getfname[0];
    }
    else
    {
        $filename = urldecode($file_url);
        $filename = basename($filename);
    }

    if (!filter_var($file_url, FILTER_VALIDATE_URL))
    {
        $send_txt = '*Error: URL Entered Is Not Valid*';
        $send_txt .= "\n";
        $send_txt .= "\n";
        $send_txt .= "*Usage:* $upload_command _<url>_///_<newfilename>_";
        include('_@sendMsg.php');
        exit();
    }

    $getcfname = @file_get_contents('_@@_customfname.txt');
    if(!empty($getcfname))
    {
        $filename = $getcfname.' - '.$filename;
    }

    $send_txt = '_Fetching File Information..._';
    $send_txt .= "\n";
    $send_txt .= "\n";
    $send_txt .= '*Filename:* '.$filename;
    include('_@sendMsg.php');

    //Get FileSize and Remote File Status
    $ch = curl_init($file_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, TRUE);
    curl_setopt($ch, CURLOPT_NOBODY, TRUE);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $data = curl_exec($ch);
    $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if($httpcode !== 200 AND $httpcode !== 206)
    {
        $send_txt = '*Error Occured: Remote File Returned Error*';
        $send_txt .= "\n";
        $send_txt .= '*Status Code: *'.$httpcode;
        $send_txt .= "\n";
        $send_txt .= '*FileURL: *'.$file_url;
        if(!empty($sent_by['username']))
        {
            $send_txt .= "\n";
            $send_txt .= '*CC: *@'.$sent_by['username'];
        }
        include('_@editMsg.php');
        include('_@sendMsg.php');
        exit();
    }

    function readableBytes($bytes)
    {
        $i = floor(log($bytes) / log(1024));
        $sizes = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        return sprintf('%.02F', $bytes / pow(1024, $i)) * 1 . ' ' . $sizes[$i];
    }

    $send_txt = '_Starting File Download..._';
    $send_txt .= "\n";
    $send_txt .= "\n";
    $send_txt .= '*Filename:* '.$filename;
    $send_txt .= "\n";
    $send_txt .= '*FileSize:* '.readableBytes($size);
    include('_@editMsg.php');

    $send_txt = '_Downloading File..._';
    $send_txt .= "\n";
    $send_txt .= "\n";
    $send_txt .= '*Filename:* '.$filename;
    $send_txt .= "\n";
    $send_txt .= '*FileSize:* '.readableBytes($size);
    include('_@editMsg.php');

//---------------------------------------------------------------------//
//  D O W N L O A D       F I L E
//---------------------------------------------------------------------//

    $tfile = fopen("t1.txt", "w");
    fwrite($tfile, base64_encode($msg['text']));
    fclose($tfile);

    $tfile = fopen("p1.txt", "w");
    fwrite($tfile, $filename);
    fclose($tfile);

    function chunked_copy()
    {
        global $filename;
        global $file_url;
        # 1 meg at a time, adjustable.
        $buffer_size = 1048576;
        # 1 GB write-chuncks
        $write_chuncks = 1073741824;
        $ret = 0;
        $fin = fopen($file_url, "rb");
        $fout = fopen($filename, "w");
        $bytes_written = 0;
        while(!feof($fin))
        {
            $bytes = fwrite($fout, fread($fin, $buffer_size));
            $ret += $bytes;
            $bytes_written += $bytes;
            if ($bytes_written >= $write_chunks)
            {
            // (another) chunck of 1GB has been written, close and reopen the stream
            fclose($fout);
            $fout = fopen($filename, "a");  // "a" for "append"
            $bytes_written = 0;  // re-start counting
            }
        }
        fclose($fin);
        fclose($fout);
        return $ret; # return number of bytes written
    }

    if(chunked_copy() > 10)
    {
        $send_txt = '_Download Completed..._';
        $send_txt .= "\n";
        $send_txt .= "\n";
        $send_txt .= '*Filename:* '.$filename;
        $send_txt .= "\n";
        $send_txt .= '*FileSize:* '.readableBytes($size);
        include('_@editMsg.php');
    }
    else
    {
        $send_txt = '*Error Occured: Failed To Download File*';
        $send_txt .= "\n";
        $send_txt .= '*FileURL: *'.$file_url;
        if(!empty($sent_by['username']))
        {
            $send_txt .= "\n";
            $send_txt .= '*CC: *@'.$sent_by['username'];
        }
        include('_@editMsg.php');
        include('_@sendMsg.php');
        unlink('t1.txt');
        unlink('p1.txt');
        @unlink($filename);
        exit();
    }

//------------------------------------------------------------------------------------//
//      U   P   L   O   D   I   N   G       F   I   L   E
//------------------------------------------------------------------------------------//

    $send_txt = '_Uploading File Now..._';
    $send_txt .= "\n";
    $send_txt .= "\n";
    $send_txt .= '*Filename:* '.$filename;
    $send_txt .= "\n";
    $send_txt .= '*FileSize:* '.readableBytes($size);
    include('_@editMsg.php');

    $uploadFieldName = 'file';
    $filePath = $filename;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.bayfiles.com/upload');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if(function_exists('curl_file_create'))
    {
        $filePath = curl_file_create($filePath);
    }
    else
    {
        $filePath = '@' . realpath($filePath);
        curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
    }
    $postFields = array($uploadFieldName => $filePath);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    $result = curl_exec($ch);
    if(curl_errno($ch))
    {
        //Error While Uploading While
        $send_txt = '*Error Occured: Failed To Upload File*';
        $send_txt .= "\n";
        $send_txt .= '*FileURL: *'.$file_url;
        if(!empty($sent_by['username']))
        {
            $send_txt .= "\n";
            $send_txt .= '*CC: *@'.$sent_by['username'];
        }
        include('_@editMsg.php');
        include('_@sendMsg.php');
        unlink('t1.txt');
        unlink('p1.txt');
        @unlink($filename);
        exit();
    }
    $fjsn = json_decode($result, true);

    if(!empty($fjsn['data']['file']['url']['short']))
    {
        $send_txt = '*Success*';
        $send_txt .= "\n";
        $send_txt .= '*Filename:* '.$filename;
        $send_txt .= "\n";
        $send_txt .= '*FileSize:* '.$fjsn['data']['file']['metadata']['size']['readable'];
        $send_txt .= "\n";
        $send_txt .= '*Bayfiles Link:* '.$fjsn['data']['file']['url']['short'];
        if(!empty($sent_by['username']))
        {
            $send_txt .= "\n";
            $send_txt .= '*CC: *@'.$sent_by['username'];
        }
        include('_@editMsg.php');
        include('_@sendMsg.php');
        @unlink('t1.txt');
        @unlink('p1.txt');
        @unlink($filename);
        exit();
    }




}

?>
