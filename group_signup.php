<!DOCTYPE html>
<html>
<head>
<title>SBR Booking - Group Allocation</title>
<link rel="stylesheet" href="style.css">
<link rel="icon" type="image/png" href="http://sbr.soc.srcf.net/booking/favicon.png"/>
</head>
<body>
<?php
include 'db_connect.php';

$sql = "SELECT * FROM hlb_events_groups WHERE group_signup = '".$_GET['ref']."'";
$res = $con_sbr->query($sql);
if(mysqli_num_rows($res)>0){
    foreach($res as $val){
        $sql_d = "SELECT * FROM hlb_events WHERE event_id = '".$val['event_id']."'";
        $res_d = $con_sbr->query($sql_d);

        $booked = explode(',',$val['group_booked']);
        $num = count($booked);
        if(in_array($_SERVER['REMOTE_USER'],$booked)){
            $crsid_book = $val['group_booked'];
            echo '<p>You are already registered in this group.</p>';
            echo "<a href='./'>Back</a>";
        }elseif($num>=4){
            $crsid_book = $val['group_booked'];
            echo '<p>The group size has already been met. The registered members are '.$crsid_book.'.</p>';
            echo "<a href='./'>Back</a>";
        }else{
            foreach($res_d as $val_d){
                $booked_d = explode(',',$val_d['group_booked']);
                if(in_array($_SERVER['REMOTE_USER'],$booked_d)){
                    echo '<p>You are already booked into another group.</p>';
                }else{
                    $booked[] = $_SERVER['REMOTE_USER'];
                    $crsid_book = implode(',',$booked);
                    echo '<p>You have been added to the group.</p>';
                    echo "<a href='./'>Back</a>";

                    $sql_b = "UPDATE hlb_events_groups SET group_booked = '".$crsid_book."' WHERE group_id = ".$val['group_id'];
                    $con_sbr->query($sql_b);
                    
                    $sql_b = "SELECT * FROM hlb_events WHERE event_id =".$val['event_id'];
                    $res_b = $con_sbr->query($sql_b);
                    foreach($res_b as $val_b){
                        $booked = explode(',',$val_b['vis_crsid_booked']);
                        $booked[] = $_SERVER['REMOTE_USER'];
                        $crsid_book = implode(',',$booked);
                        $sql_c = "UPDATE hlb_events SET vis_crsid_booked = '".$crsid_book."' WHERE event_id = ".$val['event_id'];
                        $con_sbr->query($sql_c);

                        if($val_b['event_emails']){
                            if($val_b['crsid_booked'] != ''){
                                //Generate .ics file.
                                //echo strtotime($val['event_start_datetime']);
                                //echo date_default_timezone_get();
                                //echo date('Ymd\THis', strtotime($val['event_start_datetime']));
                                $fname = "booking_".strval($val_b['event_id']).".ics";
                                //They have been added, so able to email them. 
                                $subject = "[SBR Events] Booking Confirmation";
                                $headers = "MIME-Version: 1.0" . "\r\n";
                                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                                $message = "
                                    <html>
                                    <head>
                                    <title>SBR Event Confirmation</title>
                                    </head>
                                    <body>
                                    <p>Dear ".$_SERVER['REMOTE_USER'].", <br><br> We are pleased to confirm your booking for the following event.<br><br>";

                                if($val_b['event_sub']!=''){
                                    $message .= '<b>Event Name: </b>'.$val_b['event_name'].' - '.$val_b['event_sub'].'<br>';
                                }else{
                                    $message .= '<b>Event Name: </b>'.$val_b['event_name'].'<br>';
                                }
                                $message .= '<b>Event Location: </b>'.$val_b['event_location'].'<br>';
                                $message .= '<b>Event Start Date/Time: </b>'.$val_b['event_start_datetime'].'<br>';
                                $message .= '<br>';

                                $to = $_SERVER['REMOTE_USER']."@cam.ac.uk";
                                $message .="<a href='http://sbr.soc.srcf.net/ics_files/".$fname."'>Download for Calendar</a><br><br>";
                                $message .= "To signup the rest of your household, please share this link:<br>";
                                $message .= "<a href='http://sbr.soc.srcf.net/booking/group_signup?ref=".$_GET['ref']."'>http://sbr.soc.srcf.net/booking/group_signup?ref=".$_GET['ref']."</a><br><br>";
                                $message .= "We look forward to seeing you!<br><br>
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
            
        }

    }
}else{
    echo 'This isn\'t a valid reference.';
}

?>

</body>
</html>