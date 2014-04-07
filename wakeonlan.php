<?php
if($_POST)
{
    //check if its an ajax request, exit if not
    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
        die('Request must come from Ajax.');
    } 
    
    flush();
    $port = 9;
    
    //check $_POST vars are set, exit if any missing
    if(!isset($_POST["macAddr"]))
    {
        die();
    }

    //Sanitize input data using PHP filter_var().
    $mac_Addr        = filter_var($_POST["macAddr"], FILTER_SANITIZE_STRING);
    
    //additional php validation
    if(strlen($mac_Addr)<4) // If length is less than 4 it will throw an HTTP error.
    {
        header('HTTP/1.1 500 Name is too short or empty!');
        exit();
    }
    
    //proceed with PHP
    function WakeOnLan($addr, $mac, $socket_number)
    {
       $addr_byte = explode(':', $mac);
       $hw_addr   = '';
       
       for($a=0; $a <6; $a++)
          $hw_addr .= chr(hexdec($addr_byte[$a]));
          
       $msg = chr(255).chr(255).chr(255).chr(255).chr(255).chr(255);
       
       for($a = 1; $a <= 16; $a++)
          $msg .= $hw_addr;
          
       $s = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
       
       if($s == false)
       {
          echo "Can't create socket!<BR>\n";
          echo "Error: '".socket_last_error($s)."' - " . socket_strerror(socket_last_error($s));
          return FALSE;
       }
       else
       {
          $opt_ret = socket_set_option($s, 1, 6, TRUE);
          
          if($opt_ret < 0)
          {
             echo "setsockopt() failed, error: " . strerror($opt_ret) . "<BR>\n";
             return FALSE;
          }
          
          if(socket_sendto($s, $msg, strlen($msg), 0, $addr, $socket_number))
          {
             $content = bin2hex($msg);
             //echo "Magic Packet Sent!<BR>\n";
             //echo "Data: <textarea readonly rows=\"1\" name=\"content\" cols=\"".strlen($content)."\">".$content."</textarea><BR>\n";
             //echo "Port: ".$socket_number."<br>\n";
             //echo "MAC: ".$_GET['wake_machine']."<BR>\n";
             socket_close($s);
             return TRUE;
          }
          else
          {
             echo "Magic Packet failed to send!<BR>";
             return FALSE;
          }
       }
    }
     
    $result = null;

    if($mac_Addr != "")
        $result = WakeOnLan("192.168.0.255", $mac_Addr, $port);
     
    if($result != null)
       echo "<HR>WOL ".$mac_Addr." sent!<BR>\n";
}
?>
