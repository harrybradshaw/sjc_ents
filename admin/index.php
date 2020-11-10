<html>
<head>
<title>SBR Booking - Admin</title>
<link rel="icon" type="image/png" href="http://sbr.soc.srcf.net/booking/favicon.png"/>
<link rel="stylesheet" href="./style.css">
</head>
<body>
<table style="border-collapse: collapse; width:100%;height:39px;">
<tr style="border-bottom: 2px solid #dddddd;text-align: center;border-top: 2px solid #dddddd;">
<th> <a href="./">Event View</a> </th>
<th style="font-size:16pt; width:20%;font-family:Calibri,Helvetica, Arial, sans-serif;">SBR Booking - Admin</th>
<th> <a href="?view=crsid">CRSID View </th>
</tr>
</table>

<?php

if(in_array($_SERVER['REMOTE_USER'],['hlb54','sp770','dw545','hm404','cmfg2','mms72','ms2340'])){
    include './db_connect.php';

    if($_GET['event_id']==''){
        $event_id = 38;
    }else{
        $event_id = $_GET['event_id'];
    }
    if($_GET['view']==''){
        $view = 'event';
    }else{
        $view = $_GET['view'];
    }


    if($view == 'event'){
        ?>
        <div>
        <br>
        <form method=GET action='./'>
        <select name="event_id" id="cars"  onchange="this.form.submit()">

        <option value='--Future Events--' disabled>--Future Events--</option>
        <?php
        $sql = "SELECT * FROM hlb_events WHERE event_start_datetime > NOW() ORDER BY event_start_datetime";
        $res = $con_sbr->query($sql);
        foreach($res as $val){
            if($val['event_id']==$event_id){
                ?>
                <option value=<?php echo $val['event_id']?> selected><?php echo $val['event_name'].' - '.$val['event_start_datetime'].' ('.$val['event_id'].')' ?></option>
                <?php
            }else{
                ?>
                <option value=<?php echo $val['event_id']?>><?php echo $val['event_name'].' - '.$val['event_start_datetime'].' ('.$val['event_id'].')' ?></option>
                <?php
            }
            
        }
        ?>
        <option value='--Past Events--' disabled>--Past Events--</option>
        <?php
        $sql = "SELECT * FROM hlb_events WHERE event_start_datetime < NOW() ORDER BY event_start_datetime";
        $res = $con_sbr->query($sql);
        foreach($res as $val){
            if($val['event_id']==$event_id){
                ?>
                <option value=<?php echo $val['event_id']?> selected><?php echo $val['event_name'].' - '.$val['event_start_datetime'].' ('.$val['event_id'].')' ?></option>
                <?php
            }else{
                ?>
                <option value=<?php echo $val['event_id']?>><?php echo $val['event_name'].' - '.$val['event_start_datetime'].' ('.$val['event_id'].')' ?></option>
                <?php
            }
            
        }
        ?>
        
        </select>
        </form>


        <?php
        $sql = "SELECT * FROM hlb_events WHERE event_id=".$event_id;
        $event_mailto = 'mailto:';
        $res = $con_sbr->query($sql);
        foreach($res as $val){
            echo '<b>Event Name:</b> '.$val['event_name'].'<br>';
            if($val['event_sub']!=''){
                echo '<b>Event Subtitle:</b> '.$val['event_sub'].'<br>';
            }
            echo '<b>Event Location:</b> '. $val['event_location'].'<br>';
            echo '<b>Event Start Date/Time:</b> '.$val['event_start_datetime'].'<br><br>';
            if($val['crsid_booked']==''){
                echo '<i>No members booked onto this event.</i><br>';
            }else{
                if($val['slots_vis']){
                    $bookers = explode(',',$val['vis_crsid_booked']);
                }else{ 
                    $bookers = explode(',',$val['crsid_booked']);
                }
                
                echo '<b>Capacity: </b>'.count($bookers).'/'.$val['slots_max'].'<br>';
                echo '<b>Attendees: </b>';
                //echo '<b>Attendees ('.count($bookers).'/'.$val['slots_max'].'): </b>';
                $p = 0;
                foreach($bookers as $person){
                    if($p>0){
                        echo ', ';
                    }
                    $p +=1;
                    echo '<a href="?view=crsid&crsid='.$person.'">'.$person.'</a>';
                    $event_mailto .= $person.'@cam.ac.uk;';
                }
                //echo '<br><br>';
                echo " (<a href='".$event_mailto."'>Email all attendees</a>)<br>";
            }
            if($val['event_groups']==1){
                echo '<br><b>Group Details:</b><br>';
                $sql_a = "SELECT * FROM hlb_events_groups WHERE event_id=".$event_id;
                $res_a = $con_sbr->query($sql_a);
                $q = 0;
                foreach($res_a as $val_a){
                    $group_mailto = 'mailto:';
                    $q+=1;
                    if($val_a['group_booked']!=''){
                        //echo $val_a['group_id'].'. ';
                        echo $q.'. ';
                        $bookers = explode(',',$val_a['group_booked']);
                        $p = 0;
                        foreach($bookers as $person){
                            if($p>0){
                                echo ', ';
                            }
                            $p +=1;
                            echo '<a href="?view=crsid&crsid='.$person.'">'.$person.'</a>';
                            $group_mailto.=$person.'@cam.ac.uk;';
                        }
                        echo " (<a href='".$group_mailto."'>Email Group</a>)";
                        echo'<br>';
                        

                    }else{
                        echo '<i>No group details found.</i>';
                    }
                }
                if($q==0){
                    echo '<i>No group details found.</i><br><br>';
                    ?>
                    <form method='POST' action='form_send.php'>
                    <input type='submit' value='Auto-Generate Groups'>
                    <input type="hidden" name="send_ref" value="groups_gen">
                    <?php
                    echo "<input type='hidden' name = 'event_id' value='".$val['event_id']."'>";
                    echo "<input type='hidden' name = 'event_name' value='".$val['event_name']."'>";
                    ?>
                    </form>
                    <?php
                }else{
                    ?>
                    <br>
                    <form method='POST' action='form_send.php'>
                    <input type='submit' value='Delete All Groups'> 
                    <input type="hidden" name="send_ref" value="groups_all_del">
                    <?php
                    echo "<input type='hidden' name = 'event_id' value='".$val['event_id']."'>";
                    echo "<input type='hidden' name = 'event_name' value='".$val['event_name']."'>";
                    ?>
                    </form>
                    <?php
                }
            }
            
        }

    }elseif($view='crsid'){
        ?>
        <div>
        <br>
        <form method='GET' action='./'>
        <input type='hidden' name='view' value='crsid'>
        <label for='crsid'><b>CRSID:</b></label>
        <input type='text' id='crsid' name='crsid'>
        <input type="submit"></input>
        </form>

        <br>

        <?php
        if($_GET['crsid']==''){
            
        }else{
            //Grab all events for total.
            $stmt = $con_sbr->stmt_init();
            $query = "SELECT * FROM hlb_events WHERE hlb_events.crsid_booked LIKE ? ORDER BY event_start_datetime";
            $crs = $_GET['crsid'];
            $id = "%$crs%";
            if(!$stmt->prepare($query)){
               echo "Failed to prepare statement";
            }else{
                $stmt->bind_param("s", $id);
            }
            $stmt->execute();
            $res = $stmt->get_result();
            $num_events = mysqli_num_rows($res);

            $stmt_2 = $con_sbr->stmt_init();
            $query_2 = "SELECT * FROM hlb_events_groups WHERE hlb_events_groups.group_booked LIKE ?";
            if(!$stmt_2->prepare($query_2)){
               echo "Failed to prepare statement";
            }else{
                $stmt_2->bind_param("s", $id);
            }
            $stmt_2->execute();
            $res_2 = $stmt_2->get_result();
            $num_groups = mysqli_num_rows($res_2);
            $tot = $num_events+$num_groups;

            echo 'Found '.$num_events.' event(s) for '.$_GET['crsid'].'.';
            echo '<br>';
            
            //End of all events. 
            //Do for past events.
            $stmt = $con_sbr->stmt_init();
            $query = "SELECT * FROM hlb_events WHERE (hlb_events.event_start_datetime <NOW()) AND (hlb_events.crsid_booked ) LIKE ? ORDER BY event_start_datetime";
            $crs = $_GET['crsid'];
            $id = "%$crs%";
            if(!$stmt->prepare($query)){
               echo "Failed to prepare statement";
            }else{
                $stmt->bind_param("s", $id);
            }
            $stmt->execute();
            $res = $stmt->get_result();
            
            if(mysqli_num_rows($res)!=0){
                echo '<h3>Past Events</h3>';
            }
            foreach($res as $val){
                echo '<b>Event Name:</b> <a href="?view=event&event_id='.$val['event_id'].'">'.$val['event_name'].'</a><br>';
                if($val['event_sub']!=''){
                    echo '<b>Event Subtitle:</b> '.$val['event_sub'].'<br>';
                }
                if($val['slots_vis']){
                    $bookers = explode(',',$val['vis_crsid_booked']);
                }else{ 
                    $bookers = explode(',',$val['crsid_booked']);
                }
                echo '<b>Event Location:</b> '. $val['event_location'].'<br>';
                echo '<b>Event Start Date/Time:</b> '.$val['event_start_datetime'].'<br>';
                echo '<b>Capacity: </b>'.count($bookers).'/'.$val['slots_max'].'<br>';
                echo '<b>Event Attendees: </b>';
                
                $p = 0;
                foreach($bookers as $person){
                    if($p>0){
                        echo ', ';
                    }
                    $p +=1;
                    echo '<a href="?view=crsid&crsid='.$person.'">'.$person.'</a>';
                }
                echo '<br>';
                if($val['event_groups']==1){
                    echo '<b>Sub Group: </b>';
                    $stmt_2 = $con_sbr->stmt_init();
                    $query_2 = "SELECT * FROM hlb_events_groups WHERE hlb_events_groups.event_id = ? AND hlb_events_groups.group_booked LIKE ?";
                    if(!$stmt_2->prepare($query_2)){
                    echo "Failed to prepare statement";
                    }else{
                        $id_2 = $val['event_id'];
                        $stmt_2->bind_param("ss", $id_2,$id);
                    }
                    $stmt_2->execute();
                    $res_2 = $stmt_2->get_result();
                    $q =0;
                    foreach($res_2 as $val_2){
                        if($q>0){
                            echo '; ';
                        }
                        $q +=1;
                        $bookers = explode(',',$val_2['group_booked']);
                        $p = 0;
                        foreach($bookers as $person){
                            if($p>0){
                                echo ', ';
                            }
                            $p +=1;
                            echo '<a href="?view=crsid&crsid='.$person.'">'.$person.'</a>';
                        }
                    }
                    if($q==0){
                        echo '<i>No group details found.</i>';
                    }
                    echo '<br>';
                }
                echo '<br>';
            }

            //Now for future events. 
            $stmt = $con_sbr->stmt_init();
            $query = "SELECT * FROM hlb_events WHERE hlb_events.event_start_datetime >NOW() AND hlb_events.crsid_booked LIKE ? ORDER BY event_start_datetime";
            $crs = $_GET['crsid'];
            $id = "%$crs%";
            if(!$stmt->prepare($query)){
               echo "Failed to prepare statement";
            }else{
                $stmt->bind_param("s", $id);
            }
            $stmt->execute();
            $res = $stmt->get_result();
            if(mysqli_num_rows($res)!=0){
                echo '<h3>Future Events</h3>';
            }
            foreach($res as $val){
                echo '<b>Event Name:</b> <a href="?view=event&event_id='.$val['event_id'].'">'.$val['event_name'].'</a><br>';
                if($val['event_sub']!=''){
                    echo '<b>Event Subtitle:</b> '.$val['event_sub'].'<br>';
                }
                $bookers = explode(',',$val['crsid_booked']);
                echo '<b>Event Location:</b> '. $val['event_location'].'<br>';
                echo '<b>Event Start Date/Time:</b> '.$val['event_start_datetime'].'<br>';
                echo '<b>Capacity: </b>'.count($bookers).'/'.$val['slots_max'].'<br>';
                echo '<b>Event Attendees: </b>';
                
                $p = 0;
                foreach($bookers as $person){
                    if($p>0){
                        echo ', ';
                    }
                    $p +=1;
                    echo '<a href="?view=crsid&crsid='.$person.'">'.$person.'</a>';
                }
                echo '<br>';
                if($val['event_groups']==1){
                    echo '<b>Sub Group: </b>';
                    $stmt_2 = $con_sbr->stmt_init();
                    $query_2 = "SELECT * FROM hlb_events_groups WHERE hlb_events_groups.event_id = ? AND hlb_events_groups.group_booked LIKE ?";
                    if(!$stmt_2->prepare($query_2)){
                    echo "Failed to prepare statement";
                    }else{
                        $id_2 = $val['event_id'];
                        $stmt_2->bind_param("ss", $id_2,$id);
                    }
                    $stmt_2->execute();
                    $res_2 = $stmt_2->get_result();
                    $q =0;
                    foreach($res_2 as $val_2){
                        if($q>0){
                            echo '; ';
                        }
                        $q +=1;
                        $bookers = explode(',',$val_2['group_booked']);
                        $p = 0;
                        foreach($bookers as $person){
                            if($p>0){
                                echo ', ';
                            }
                            $p +=1;
                            echo '<a href="?view=crsid&crsid='.$person.'">'.$person.'</a>';
                        }
                    }
                    if($q==0){
                        echo '<i>No group details found.</i>';
                    }
                    echo '<br>';
                }
                echo '<br>';
            }
            echo '<br>';
            echo '<a href="https://www.lookup.cam.ac.uk/person/crsid/'.$_GET['crsid'].'">Univeristy Lookup for '.$_GET['crsid'].'</a><br><br>';
        }
        

        ?>

        </div>
        <?php

    }
}else{
    header('location:../booking/');
}
?>
</div>

<footer class="content-footer">
    <hr>
    <p>Created by <a href="https://www.lookup.cam.ac.uk/person/crsid/hlb54">Harry Bradshaw</a></p>
</footer>

</body>
</html>