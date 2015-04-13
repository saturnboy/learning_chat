<?php
$fp = fopen('msgs.txt', 'a+') or die('no chat log');

if (flock($fp, LOCK_EX)) {  // acquire lock
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        //on GET, read chat log and return it
        $msgs = array();
        while($msg = fgets($fp)) {
            $i = strpos($msg, '|');
            if ($i) {
                $msgs[] = array( 'name' => substr($msg, 0, $i), 'msg' => trim(substr($msg, $i+1)) );
            }
        }
        echo json_encode($msgs);
    } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
        //on POST, write msg to chat log
        if (isset($_POST) && isset($_POST['name']) && isset($_POST['msg'])) {
            fwrite($fp, $_POST['name'] . '|' . $_POST['msg'] . "\n");
            fflush($fp);
        } else {
            //bad msg
            http_response_code(400);
        }
    }
} else {
    // failed to aquire lock
}

flock($fp, LOCK_UN); // release lock
fclose($fp);
