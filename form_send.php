<?php
include 'db_connect.php';
//date_default_timezone_set('UTC');
function dateToCal($time) {
    //return date('Ymd\THis', $time) . 'Z';
    return date('Ymd\THis', $time);
}
function random_str(
    int $length = 64,
    string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
): string {
    if ($length < 1) {
        throw new \RangeException("Length must be a positive integer");
    }
    $pieces = [];
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $pieces []= $keyspace[random_int(0, $max)];
    }
    return implode('', $pieces);
}

$sql = "SELECT max(log_id) FROM hlb_events_log";
$res = $con_sbr->query($sql);
foreach($res as $val){
    $new_log_id = $val['max(log_id)'] + 1;
}

if($_POST['send_ref']=='event_book'){
    if($_POST['event_id']!=''){
        if($_POST['unbook']){
            $sql = "SELECT * FROM hlb_events WHERE event_id =".$_POST['event_id'];
            $res = $con_sbr->query($sql);
            foreach($res as $val){
                $booked = explode(',',$val['crsid_booked']);
                if(in_array($_POST['event_bookee'],$booked)){
                    $unbooked = array_diff($booked,[$_POST['event_bookee']]);
                    if($val['slots_vis']){
                        $sql_b = "SELECT * FROM hlb_events_groups WHERE event_id =".$_POST['event_id']." AND group_booked LIKE '%".$_POST['event_bookee']."%'";
                        echo $sql_b;
                        $res_b = $con_sbr->query($sql_b);
                        foreach($res_b as $val_b){
                            $booked_b = explode(',',$val_b['group_booked']);
                            if(count($booked_b)>1){
                                if(in_array($_POST['event_bookee'],$booked_b)){
                                    $unbooked_b = array_diff($booked_b,[$_POST['event_bookee']]);
                                    $unbooked[] = $unbooked_b[0];
                                }
                            }
                            
                        }
                    }
                    $crsid_book = implode(',',$unbooked);
                    $sql = "UPDATE hlb_events SET crsid_booked = '".$crsid_book."' WHERE event_id =".$_POST['event_id'];
                    $con_sbr->query($sql);
                }
            }
            


            //Need to remove them from their allocated group also.
            $sql = "SELECT * FROM hlb_events_groups WHERE event_id =".$_POST['event_id'];
            $res = $con_sbr->query($sql);
            foreach($res as $val){
                $booked = explode(',',$val['group_booked']);
                if(in_array($_POST['event_bookee'],$booked)){
                    $unbooked = array_diff($booked,[$_POST['event_bookee']]);
                    $crsid_book = implode(',',$unbooked);
                    $sql = "UPDATE hlb_events_groups SET group_booked = '".$crsid_book."' WHERE group_id =".$val['group_id'];
                    $con_sbr->query($sql);
                }
            }
    
            $sql = "INSERT INTO hlb_events_log (`log_time`,`log_user`, `log_event_id`, `log_event_name`, `log_event_action`, `log_id`)  VALUES (NOW(), '".$_POST['event_bookee']."','".$_POST['event_id']."', '".$_POST['event_name']."', 'Cancel', '".$new_log_id."')";
            $con_sbr->query($sql);

            $sql = "SELECT * FROM hlb_events WHERE event_id =".$_POST['event_id'];
            $res = $con_sbr->query($sql);
            foreach($res as $val){
                if($val['event_emails']){
                    if($val['crsid_booked'] != ''){
                        $booked = explode(',',$val['crsid_booked']);
                        if(!in_array($_POST['event_bookee'],$booked)){
                            //They have been removed, so able to email them. 
                            $subject = "[SBR Events] Booking Cancellation";
                            $headers = "MIME-Version: 1.0" . "\r\n";
                            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                            $message = "
                                <html>
                                <head>
                                <title>SJC Ents Cancellation</title>
                                </head>
                                <body>
                                <p>Dear ".$_POST['event_bookee'].", <br><br> Your booking for the following event has been cancelled.<br><br>";

                            if($val['event_sub']!=''){
                                $message .= '<b>Event Name: </b>'.$val['event_name'].' - '.$val['event_sub'].'<br>';
                            }else{
                                $message .= '<b>Event Name: </b>'.$val['event_name'].'<br>';
                            }
                            $message .= '<b>Event Location: </b>'.$val['event_location'].'<br>';
                            $message .= '<b>Event Start Date/Time: </b>'.$val['event_start_datetime'].'<br>';
                            $message .= '<br>';

                            $to = $_POST['event_bookee']."@cam.ac.uk";
                            $message .= "
                            From,
                            <br>
                            SJC Ents</p>
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


    
        }elseif($_POST['book']){
            $sql = "SELECT * FROM hlb_events WHERE event_id =".$_POST['event_id'];
            $res = $con_sbr->query($sql);
            foreach($res as $val){
                if($val['crsid_booked'] == ''){
                    $crsid_book = $_POST['event_bookee'];
                }else{
                    $booked = explode(',',$val['crsid_booked']);
                    $num = count($booked);
                    if(in_array($_POST['event_bookee'],$booked)){
                        $crsid_book = $val['crsid_booked'];
                    }elseif($num>=$val['slots_max']){
                        $crsid_book = $val['crsid_booked'];
                    }else{
                        $booked[] = $_POST['event_bookee'];
                        $crsid_book = implode(',',$booked);
                    }
                }
            }
            
            $sql = "UPDATE hlb_events SET crsid_booked = '".$crsid_book."' WHERE event_id =".$_POST['event_id'];
            $con_sbr->query($sql);

    
            $sql = "INSERT INTO hlb_events_log (`log_time`,`log_user`, `log_event_id`, `log_event_name`, `log_event_action`, `log_id`)  VALUES (NOW(), '".$_POST['event_bookee']."','".$_POST['event_id']."', '".$_POST['event_name']."', 'Book', '".$new_log_id."')";
            $con_sbr->query($sql);
            
            //Event conf emails
            $sql = "SELECT * FROM hlb_events WHERE event_id =".$_POST['event_id'];
            $res = $con_sbr->query($sql);
            foreach($res as $val){
                if($val['event_emails']){
                    if($val['crsid_booked'] != ''){
                        $booked = explode(',',$val['crsid_booked']);
                        if(in_array($_POST['event_bookee'],$booked)){
                            //Generate .ics file.
                            //echo strtotime($val['event_start_datetime']);
                            //echo date_default_timezone_get();
                            //echo date('Ymd\THis', strtotime($val['event_start_datetime']));
                            $fname = "booking_".strval($val['event_id']).".ics";
                            $myfile = fopen("ics_files/".$fname, "w") or die("Unable to open file!");
                            $txt = "BEGIN:VCALENDAR\n".
                            "VERSION:2.0\n".
                            "PRODID:SBREVENTS\n".
                            "CALSCALE:GREGORIAN\n".
                            "BEGIN:VEVENT\n".
                            "DTSTAMP:" . time() ."\n".
                            "DTSTART:" . dateToCal(strtotime($val['event_start_datetime'])) ."\n".
                            "DTEND:" . dateToCal(strtotime($val['event_end_datetime'])) ."\n".
                            "LOCATION:" . addslashes($val['event_location']) ."\n".
                            "DESCRIPTION:" . addslashes($val['event_desc']) ."\n".
                            "SUMMARY:".addslashes($val['event_name'])."\n".
                            "UID:" . md5($val['event_name'])."\n".
                            "END:VEVENT\n".
                            "END:VCALENDAR";
                            fwrite($myfile, $txt);
                            fclose($myfile);

                            //They have been added, so able to email them. 
                            $subject = "[SJC Ents] Booking Confirmation";
                            $headers = "MIME-Version: 1.0" . "\r\n";
                            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                            $message = "
                                <html>
                                <head>
                                <title>SJC Event Confirmation</title>
                                </head>
                                <body>
                                <p>Dear ".$_POST['event_bookee'].", <br><br> We are pleased to confirm your booking for the following event.<br><br>";

                            if($val['event_sub']!=''){
                                $message .= '<b>Event Name: </b>'.$val['event_name'].' - '.$val['event_sub'].'<br>';
                            }else{
                                $message .= '<b>Event Name: </b>'.$val['event_name'].'<br>';
                            }
                            $message .= '<b>Event Location: </b>'.$val['event_location'].'<br>';
                            $message .= '<b>Event Start Date/Time: </b>'.$val['event_start_datetime'].'<br>';
                            $message .= '<br>';

                            $to = $_POST['event_bookee']."@cam.ac.uk";
                            $message .="<a href='http://sjcents.com/ics_files/".$fname."'>Download for Calendar</a><br><br>";
                            $message .= "We look forward to seeing you!<br>
                            If you can no longer make the event, please cancel your booking <a href='http://sjcents.com'>here</a>.<br>
                            <br> 
                            From,
                            <br>
                            SJC Ents</p>
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
    if($_POST['ref_page']=='myevents'){
        header('location:./'.$_POST['ref_page']);
    }else{
        header('location:./event?id='.$_POST['ref_page']);
    }
   
    die();
}elseif($_POST['send_ref']=='groups_add'){
    $chk = 0;
    if($_POST['group_list']!=''){
        //Create a new group_id
        $sql = "SELECT max(group_id) FROM hlb_events_groups";
        $res = $con_sbr->query($sql);
        foreach($res as $val){
            $new_group_id = $val['max(group_id)'] + 1;
        }

        //prepare the CRSID
        $g_string = rtrim(trim($_POST['group_list']),',');
        $g_list = explode(',',$g_string);
        $booking_needed = [];
        foreach($g_list as &$person){
            $person = trim($person);
            $id = "%$person%";
            $stmt = $con_sbr->stmt_init();
            $name = $_POST['event_name'];
            $e_id = $_POST['event_id'];
            $query = "SELECT * FROM hlb_events WHERE event_id = ? AND crsid_booked LIKE ?";
            $stmt->prepare($query);
            $stmt->bind_param("is",$e_id,$id);
            $stmt->execute();
            $res = $stmt->get_result();
            if(mysqli_num_rows($res)==0){
                $booking_needed[] = $person;
            }
        }

        $check_already = [];
        $sql_b = "SELECT * FROM hlb_events_groups WHERE hlb_events_groups.event_name = '".$_POST['event_name']."'";
        $res_b = $con_sbr->query($sql_b);
        //echo 'Checker found: ';
        //echo (mysqli_num_rows($res_b));
        //echo ' ';
        foreach($res_b as $val_b){
            $check = explode(',',$val_b['group_booked']);
            foreach($g_list as $pep2){
                if(in_array($pep2,$check)){
                    //echo 'In array: '.$pep2;
                    $check_already[] = $pep2;
                }
            }
            
        }
        
        if(count($check_already)>0){
            $chk=4;
            $booking_needed = $check_already;
        }else{
            $found = count($g_list) - count($booking_needed);

            $g_string = implode(',',$g_list);
            echo $new_group_id.': ';
            echo $g_string;

            if(count($booking_needed)==0){
                //Good!
                $chk = 1;
                $stmt = $con_sbr->stmt_init();
                $query = "INSERT INTO hlb_events_groups (`event_id`,`event_name`,`group_booked`) VALUES (?, ?, ?)";
                
                if(!$stmt->prepare($query)){
                    echo "Failed to prepare statement";
                }else{
                    $id = $_POST['event_id'];
                    $name = $_POST['event_name'];
                    $stmt->bind_param("iss", $id, $name, $g_string);
                    $stmt->execute();
                }
            }else{
                //Not so good...
                $chk = 2;
                $sql = "SELECT * FROM hlb_events WHERE event_id=".$e_id;
                $res = $con_sbr->query($sql);
                
                foreach($res as $val){
                    $booked = explode(',',$val['crsid_booked']);
                    $num = count($booked);
                    if($val['crsid_booked']==''){
                        $num = 0;
                    }
                    $spaces = $val['slots_max'] - $num;
                }
                if($spaces<count($booking_needed)){
                    //Not enough spaces
                    $chk=3;
                }else{
                    //Insert into the booking table
                    echo 'Need to book ';
                    if($num==0){
                        $event_booked = implode(',',$booking_needed);
                    }else{
                        $event_booked = $val['crsid_booked'].','.implode(',',$booking_needed);
                    }

                    echo $e_id;
                    $stmt = $con_sbr->stmt_init();
                    $query = "UPDATE hlb_events SET crsid_booked = ? WHERE event_id = ?";
                    $stmt->prepare($query);
                    $stmt->bind_param("si",$event_booked,$e_id);
                    $stmt->execute();

                    //Now insert into the groups table
                    $stmt = $con_sbr->stmt_init();
                    $query = "INSERT INTO hlb_events_groups (`event_id`,`event_name`,`group_booked`) VALUES (?, ?, ?)";
            
                    if(!$stmt->prepare($query)){
                        echo "Failed to prepare statement";
                    }else{
                        $id = $_POST['event_id'];
                        $name = $_POST['event_name'];
                        $stmt->bind_param("iss", $id, $name, $g_string);
                        $stmt->execute();
                    }

                    //Now into the logging table
                    
                    $stmt = $con_sbr->stmt_init();
                    $query = "INSERT INTO hlb_events_log (`log_time`,`log_user`,`log_event_id`,`log_event_name`,`log_event_action`, `log_id`) VALUES (NOW(),?, ?, ?, 'Groups Book', ?)";
            
                    if(!$stmt->prepare($query)){
                        echo "Failed to prepare statement";
                    }else{
                        $id = $_POST['event_id'];
                        $name = $_POST['event_name'];
                        $book_str = implode(',',$booking_needed);
                        $stmt->bind_param("sisi", $book_str, $id, $name,$new_log_id);
                        $stmt->execute();
                    }

                }  
            }
            
        }
  
    }
    if($chk==0){
        header('location:../booking/groups');

    }else{
        if(implode(',',$booking_needed)==''){
            header('location:../booking/groups?chk='.$chk.'&found='.$found.'&group_id='.$new_group_id.'&event_id='.$_POST['event_id']);
        }else{
            header('location:../booking/groups?chk='.$chk.'&found='.$found.'&group_id='.$new_group_id.'&booked='.implode(',',$booking_needed).'&event_id='.$_POST['event_id']);
        }
    }
    
    die();

}elseif($_POST['send_ref']=='groups_del'){
    if($_POST['group_id']){
        $sql="DELETE FROM hlb_events_groups WHERE group_id=".$_POST['group_id'];
        $con_sbr->query($sql);
    }
    header('location:../booking/groups?event_id='.$_POST['event_id']);
    die();
}elseif($_POST['send_ref']=='groups_all_del'){
    if($_POST['event_id']){
        $sql="DELETE FROM hlb_events_groups WHERE hlb_events_groups.event_id=".$_POST['event_id'];
        $con_sbr->query($sql);
    }
    header('location:../booking/admin?event_id='.$_POST['event_id']);
    die();
}elseif($_POST['send_ref']=='groups_gen'){
    $sql = "SELECT * FROM hlb_events WHERE event_id=".$_POST['event_id'];
    $res = $con_sbr->query($sql);
    foreach($res as $val){
        if($val['crsid_booked']!=''){
            $booked = explode(',',$val['crsid_booked']);
            shuffle($booked);
            $num_groups = floor(count($booked)/6) + 1;
            echo $num_groups.'<br>';
            //echo count($booked).'<br>';
            $left = count($booked) - ($num_groups-1)*6;
            $target = 5;
            $num_smaller = $target - $left +1;
            $num_bigger = $num_groups - $num_smaller;
            for($x = 0;$x<$num_bigger;$x++){
                echo $x.': ';
                $g_string = '';
                for($y = 0;$y<6;$y++){
                    if($booked[($x*6)+$y]){
                        $g_string.= $booked[($x*6)+$y];
                    }
                    if($y<5){
                        $g_string.= ',';
                    }                   
                }
                echo $g_string.'<br>';
                $query = "INSERT INTO hlb_events_groups (`event_id`,`event_name`,`group_booked`) VALUES (?, ?, ?)";
                $sql_b = "INSERT INTO hlb_events_groups (`event_id`,`event_name`,`group_booked`) VALUES (".$_POST['event_id'].",'".$_POST['event_name']."','".$g_string."')";
                //echo $sql_b.'<br>';
                $con_sbr->query($sql_b);
                
            }
            for($x = $num_bigger;$x<$num_groups;$x++){
                echo $x.': ';
                $g_string = '';
                for($y = 0;$y<$target;$y++){
                    if($booked[(($num_bigger)*6)+(($x-$num_bigger)*$target)+$y]){
                        $g_string.= $booked[(($num_bigger)*6)+(($x-$num_bigger)*$target)+$y];
                    }
                    if($y<$target-1){
                        $g_string.= ',';
                    }                   
                }
                echo $g_string.'<br>';
                $query = "INSERT INTO hlb_events_groups (`event_id`,`event_name`,`group_booked`) VALUES (?, ?, ?)";
                $sql_b = "INSERT INTO hlb_events_groups (`event_id`,`event_name`,`group_booked`) VALUES (".$_POST['event_id'].",'".$_POST['event_name']."','".$g_string."')";
                //echo $sql_b.'<br>';
                $con_sbr->query($sql_b);
                
                
            }
            
        }
    }
    header('location:../booking/admin?event_id='.$_POST['event_id']);
    die();
}elseif($_POST['send_ref']=='event_book_allocation'){
    if($_POST['event_id']!=''){
        if($_POST['unbook']){
            $sql = "SELECT * FROM hlb_events WHERE event_id =".$_POST['event_id'];
            $res = $con_sbr->query($sql);
            foreach($res as $val){
                $booked = explode(',',$val['crsid_booked']);
                $unbooked = array_diff($booked,[$_POST['event_bookee']]);
            }
            $crsid_book = implode(',',$unbooked);
            $sql = "UPDATE hlb_events SET crsid_booked = '".$crsid_book."' WHERE event_id =".$_POST['event_id'];
            $con_sbr->query($sql);
    
            $sql = "INSERT INTO hlb_events_log (`log_time`,`log_user`, `log_event_id`, `log_event_name`, `log_event_action`, `log_id`)  VALUES (NOW(), '".$_POST['event_bookee']."','".$_POST['event_id']."', '".$_POST['event_name']."', 'Cancel', '".$new_log_id."')";
            $con_sbr->query($sql);

            $sql = "SELECT * FROM hlb_events WHERE event_id =".$_POST['event_id'];
            $res = $con_sbr->query($sql);
            foreach($res as $val){
                if($val['event_emails']){
                    if($val['crsid_booked'] != ''){
                        $booked = explode(',',$val['crsid_booked']);
                        if(!in_array($_POST['event_bookee'],$booked)){
                            //They have been removed, so able to email them. 
                            $subject = "[SJC Ents] Booking Cancellation";
                            $headers = "MIME-Version: 1.0" . "\r\n";
                            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                            $message = "
                                <html>
                                <head>
                                <title>SBR Event Cancellation</title>
                                </head>
                                <body>
                                <p>Dear ".$_POST['event_bookee'].", <br><br> Your booking for the following event has been cancelled.<br><br>";

                            if($val['event_sub']!=''){
                                $message .= '<b>Event Name: </b>'.$val['event_name'].' - '.$val['event_sub'].'<br>';
                            }else{
                                $message .= '<b>Event Name: </b>'.$val['event_name'].'<br>';
                            }
                            $message .= '<b>Event Location: </b>'.$val['event_location'].'<br>';
                            $message .= '<b>Event Start Date/Time: </b>'.$val['event_start_datetime'].'<br>';
                            $message .= '<br>';

                            $to = $_POST['event_bookee']."@cam.ac.uk";
                            $message .= "
                            From,
                            <br>
                            SJC Ents</p>
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


    
        }elseif($_POST['book']){
            $sql = "SELECT * FROM hlb_events WHERE event_id =".$_POST['event_id'];
            $res = $con_sbr->query($sql);
            foreach($res as $val){
                if($val['crsid_booked'] == ''){
                    $crsid_book = $_POST['event_bookee'];
                }else{
                    $booked = explode(',',$val['crsid_booked']);
                    $num = count($booked);
                    if(in_array($_POST['event_bookee'],$booked)){
                        $crsid_book = $val['crsid_booked'];
                    }elseif($num>=$val['slots_max']){
                        $crsid_book = $val['crsid_booked'];
                    }else{
                        $booked[] = $_POST['event_bookee'];
                        $crsid_book = implode(',',$booked);
                    }
                }
            }
            
            $sql = "UPDATE hlb_events SET crsid_booked = '".$crsid_book."' WHERE event_id =".$_POST['event_id'];
            $con_sbr->query($sql);


            $sql = "INSERT INTO hlb_events_log (`log_time`,`log_user`, `log_event_id`, `log_event_name`, `log_event_action`, `log_id`)  VALUES (NOW(), '".$_POST['event_bookee']."','".$_POST['event_id']."', '".$_POST['event_name']."', 'Book', '".$new_log_id."')";
            $con_sbr->query($sql);
            $rand = random_str(10);
            //This is the new bit...
            $sql = "INSERT INTO hlb_events_groups (`event_id`,`event_name`,`group_booked`,`group_signup`) VALUES ('".$_POST['event_id']."', '".$_POST['event_name']."', '".$_POST['event_bookee']."','".$rand."')";
            $con_sbr->query($sql);
            
            //Event conf emails
            $sql = "SELECT * FROM hlb_events WHERE event_id =".$_POST['event_id'];
            $res = $con_sbr->query($sql);
            foreach($res as $val){
                if($val['event_emails']){
                    if($val['crsid_booked'] != ''){
                        $booked = explode(',',$val['crsid_booked']);
                        if(in_array($_POST['event_bookee'],$booked)){
                            //Generate .ics file.
                            //echo strtotime($val['event_start_datetime']);
                            //echo date_default_timezone_get();
                            //echo date('Ymd\THis', strtotime($val['event_start_datetime']));
                            $fname = "booking_".strval($val['event_id']).".ics";
                            $myfile = fopen("../ics_files/".$fname, "w") or die("Unable to open file!");
                            $txt = "BEGIN:VCALENDAR\n".
                            "VERSION:2.0\n".
                            "PRODID:SBREVENTS\n".
                            "CALSCALE:GREGORIAN\n".
                            "BEGIN:VEVENT\n".
                            "DTSTAMP:" . time() ."\n".
                            "DTSTART:" . dateToCal(strtotime($val['event_start_datetime'])) ."\n".
                            "DTEND:" . dateToCal(strtotime($val['event_end_datetime'])) ."\n".
                            "LOCATION:" . addslashes($val['event_location']) ."\n".
                            "DESCRIPTION:" . addslashes($val['event_desc']) ."\n".
                            "SUMMARY:".addslashes($val['event_name'])."\n".
                            "UID:" . md5($val['event_name'])."\n".
                            "END:VEVENT\n".
                            "END:VCALENDAR";
                            fwrite($myfile, $txt);
                            fclose($myfile);

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
                                <p>Dear ".$_POST['event_bookee'].", <br><br> We are pleased to confirm your booking for the following event.<br><br>";

                            if($val['event_sub']!=''){
                                $message .= '<b>Event Name: </b>'.$val['event_name'].' - '.$val['event_sub'].'<br>';
                            }else{
                                $message .= '<b>Event Name: </b>'.$val['event_name'].'<br>';
                            }
                            $message .= '<b>Event Location: </b>'.$val['event_location'].'<br>';
                            $message .= '<b>Event Start Date/Time: </b>'.$val['event_start_datetime'].'<br>';
                            $message .= '<br>';

                            $to = $_POST['event_bookee']."@cam.ac.uk";
                            $message .="<a href='http://sbr.soc.srcf.net/ics_files/".$fname."'>Download for Calendar</a><br><br>";
                            $message .= "To signup the rest of your household, please share this link:<br>";
                            $message .= "<a href='http://sbr.soc.srcf.net/booking/group_signup?ref=".$rand."'>http://sbr.soc.srcf.net/booking/group_signup?ref=".$rand."</a><br><br>";
                            $message .= "We look forward to seeing you!<br>
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
    header('location:../booking/');
    die();
}elseif($_POST['send_ref']=='event_create'){
    if($_POST['event_open']){
        $e_o = 1;
        
    }else{
        $e_o = 0;
    }
    if($_POST['event_vis']){
        $e_v = 1;
        
    }else{
        $e_v = 0;
    }
    echo $e_o;
    echo $e_v;

    if (!($stmt = $con_sbr->prepare("INSERT INTO hlb_events (event_name,event_sub,event_desc,event_location,event_start_datetime,event_end_datetime,slots_max,event_vis,event_open) VALUES (?,?,?,?,?,?,?,?,?)"))) {
        echo "Prepare failed: (" . $con_sbr->errno . ") " . $con_sbr->error;
    }
    
    if (!($stmt->bind_param("ssssssiii",$_POST['event_name'],$_POST['event_sub'],$_POST['event_desc'],$_POST['event_location'],$_POST['event_start_datetime'],$_POST['event_end_datetime'],$_POST['slots_max'],intval($e_v),intval($e_o)))){
        echo "Bind failed: (" . $stmt->errno . ") " . $stmt->error;
    }
    if (!$stmt->execute()) {
        echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
    }    
    $stmt->close();
    header('location:./');
    die();
}elseif($_POST['send_ref']=='event_edit'){
    if($_POST['event_open']){
        $e_o = 1;
        
    }else{
        $e_o = 0;
    }
    if($_POST['event_vis']){
        $e_v = 1;
        
    }else{
        $e_v = 0;
    }
    echo $e_o;
    echo $e_v;

    if (!($stmt = $con_sbr->prepare("UPDATE hlb_events SET event_reglink=?, event_zoomlink = ?, event_fblink = ?, event_imgsrc = ?, event_name = ? ,event_sub =? ,event_subname =? ,event_desc = ?,event_location = ?,event_start_datetime = ?,event_end_datetime =? ,slots_max = ?,event_vis = ?,event_open = ? WHERE event_id=".$_POST['event_id']))) {
        echo "Prepare failed: (" . $con_sbr->errno . ") " . $con_sbr->error;
    }
    
    if (!($stmt->bind_param("sssssssssssiii",$_POST['event_reglink'],$_POST['event_zoomlink'],$_POST['event_fblink'],$_POST['event_imgsrc'],$_POST['event_name'],$_POST['event_sub'],$_POST['event_subname'],$_POST['event_desc'],$_POST['event_location'],$_POST['event_start_datetime'],$_POST['event_end_datetime'],$_POST['slots_max'],intval($e_v),intval($e_o)))){
        echo "Bind failed: (" . $stmt->errno . ") " . $stmt->error;
    }
    if (!$stmt->execute()) {
        echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
    }    
    $stmt->close();
    header('location:./admin/?event_id='.$_POST['event_id']);
    die();
}
else{
    header('location:./');
    die();
}


?>