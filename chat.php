<?php
$fp = fopen('msgs.txt', 'a+') or die('no chat log');

if (flock($fp, LOCK_EX)) {  // acquire lock
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        //on GET
        $msgs = array();
        
        //read log file
        while ($msg = trim(fgets($fp))) {
            $i = strpos($msg, '|');
            if ($i) {
                $msgs[] = array( 'name' => substr($msg, 0, $i), 'msg' => substr($msg, $i+1) );
            }
        }
        
        //return log
        header('Content-Type: application/json');
        echo json_encode($msgs);
    } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
        //on POST
        if (isset($_POST) && isset($_POST['name']) && isset($_POST['msg'])) {
        	//validation goes here...
        	
        	//write msg to log file
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
