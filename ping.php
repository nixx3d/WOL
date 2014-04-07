<?php
if($_POST)
{
    //check if its an ajax request, exit if not
    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
        die('Request must come from Ajax.');
    } 
    
    flush();
    $farm = array ('192.168.0.2','192.168.0.3','192.168.0.4','192.168.0.11','192.168.0.12','192.168.0.13','192.168.0.14','192.168.0.15','192.168.0.16','192.168.0.17','192.168.0.18','192.168.0.5');
    $port = 135;
    $timeout = 0.001;
    $pingResults = null;
    
    //check $_POST vars are set, exit if any missing
    if(!isset($_POST["id"]))
    {
        die();
    }

    //Sanitize input data using PHP filter_var().
    $id        = filter_var($_POST["id"], FILTER_SANITIZE_STRING);
    
    //additional php validation
    if(strlen($id)<2) // If length is less than 4 it will throw an HTTP error.
    {
        header('HTTP/1.1 500 Name is too short or empty!');
        exit();
    }
    
    //proceed with PHP
    function pingAll($farm, $port, $timeout)
    {
      $total_start = microtime(true); 

      //per item loop
      foreach ($farm as $pc){
        $time_start = microtime(true); 
        $check = @fsockopen($pc,$port,$errCode,$errStr,$timeout);

        if (is_resource($check))
        {
           //echo $pc . " online <BR>";
           $pingResults[] = 'online';
           fclose($check);
           //return TRUE;
        }
        else
        {
           //echo $pc . " offline <BR>";
           $pingResults[] = 'offline';
           //return FALSE;
        }

        //per item execute time
        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start);
        //echo 'seconds: ' .$execution_time.'<BR><BR>';
      }

      //total execute time
      $total_end = microtime(true);
      $execution_time = ($total_end - $total_start);
      //echo 'Total seconds: ' .$execution_time.'<BR><BR>';
      echo json_encode($pingResults);
    }
     
    $result = null;

    if($id != "") {
        $result = pingAll($farm, $port, $timeout);
    }
    if($result != null) {
       //echo "<HR>Ping ".$id." sent!<BR>\n";
    }
}
?>
