<?php
include 'db_connect.php';
// the message
//$msg = "HAHAHAHA LOOK WHAT I CAN DO";
$subject = "[SBR Events] Booking Reminder - TOMORROW";
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
//$headers .= 'From: <webmaster@example.com>' . "\r\n";
//$headers .= 'Cc: myboss@example.com' . "\r\n";

// use wordwrap() if lines are longer than 70 characters

//Grab the first event on the day.

$sql = "SELECT * FROM hlb_events WHERE DATE(NOW()) = DATE(DATE_ADD(hlb_events.event_start_datetime, INTERVAL -1 DAY))";
$res = $con_sbr->query($sql);
$emailed =[];
if(mysqli_num_rows($res)==0){
    
}else{
    //for each event tomorrow
    foreach($res as $val){
        //if the event has people booked onto it
        if($val['crsid_booked']!=''){
            $booked = explode(',',$val['crsid_booked']);
            //for each person booked on
            foreach($booked as $person){
                //if we haven't already emailed them.
                if(!in_array($person,$emailed)){
                    $emailed[] = $person;
                    //Find all their events.
                    $sql_b= "SELECT * FROM hlb_events WHERE hlb_events.crsid_booked LIKE '%".$person."%' AND DATE(NOW()) = DATE(DATE_ADD(event_start_datetime, INTERVAL -1 DAY)) AND event_start_datetime > NOW() ORDER BY event_start_datetime";
                    $res_b = $con_sbr->query($sql_b);
                    $message = "
                    <html>
                    <head>
                    <title>SBR Event Reminder</title>
                    </head>
                    <body>
                    <p>Dear ".$person.", <br><br> Please see the below reminder(s) for events booked tomorrow.<br><br>";

                    foreach($res_b as $val_b){
                        if($val_b['event_sub']!=''){
                            $message .= '<b>Event Name: </b>'.$val_b['event_name'].' - '.$val_b['event_sub'].'<br>';
                        }else{
                            $message .= '<b>Event Name: </b>'.$val_b['event_name'].'<br>';
                        }
                        $message .= '<b>Event Location: </b>'.$val_b['event_location'].'<br>';
                        $message .= '<b>Event Start Date/Time: </b>'.$val_b['event_start_datetime'].'<br>';
                        $message .= '<br>';
                    }
                    $to = $person."@cam.ac.uk";
                    $message .= "We look forward to seeing you!<br>
                    <br>
                    If you can no longer make the event, please cancel your booking <a href='http://sbr.soc.srcf.net/booking'>here</a>.<br>
                    <br> 
                    From,
                    <br>
                    The SBR Committee</p>
                    </body>
                    </html>
                    ";

                    // send email
                    if($headers!=''){
                        mail($to,$subject,$message,$headers);
                    }else{
                        mail($to,$subject,$message);
                    }
                
                }
            }
            
        }
        
    }
}



?>