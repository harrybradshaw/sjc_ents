<html>
<head>
<title>SBR Booking - Groups</title>
<link rel="stylesheet" href="style.css">
<link rel="icon" type="image/png" href="http://sbr.soc.srcf.net/booking/favicon.png"/>
</head>
<body>
<table style="border-collapse: collapse; width:100%;height:39px;">
<tr style="border-bottom: 2px solid #dddddd;text-align: center;border-top: 2px solid #dddddd;">
<th style="font-size:16pt; width:20%;font-family:Calibri,Helvetica, Arial, sans-serif;"><a href='groups'style="font-size:16pt; width:20%;font-family:Calibri,Helvetica, Arial, sans-serif;">SBR Booking - Groups</a></th>
</tr>
</table>
<p>
<?php
include 'db_connect.php';

if($_GET['chk']==''){
    
}elseif($_GET['chk']==1){
    echo 'Success - Found all '.$_GET['found'].' members.';
    echo '<br>';
    $sql = "SELECT group_booked FROM hlb_events_groups WHERE group_id =".$_GET['group_id'];
    $res = $con_sbr->query($sql);
    if(mysqli_num_rows($res)!=0){
        foreach($res as $val){
            echo 'Confirmed group registration: '.$val['group_booked'];
        }
    }else{
        echo 'Group addition failed.';
    }
    
    echo '<br>';
    //echo '<br>';
}elseif($_GET['chk']==2){
    echo 'From request, only '.$_GET['found'].' person/people booked.';
    echo '<br>';
    if($_GET['booked']!=''){
        echo 'Needed to book: '.$_GET['booked'].'.';
        echo '<br>';
    }
    $sql = "SELECT group_booked FROM hlb_events_groups WHERE group_id =".$_GET['group_id'];
    $res = $con_sbr->query($sql);
    if(mysqli_num_rows($res)!=0){
        foreach($res as $val){
            echo 'Confirmed group registration: '.$val['group_booked'];
        }
    }else{
        echo 'Group addition failed.';
    }
    echo '<br>';
    //echo '<br>';
}elseif($_GET['chk']==3){
    echo 'Event at Capacity - Could not add whole group.';
    echo '<br>';
    if($_GET['booked']!=''){
        echo "Couldn't book: ".$_GET['booked'].'.';
        echo '<br>';
    }
    
}elseif($_GET['chk']==4){
    echo 'User already booked in - Could not add whole group.';
    echo '<br>';
    if($_GET['booked']!=''){
        echo "Checked in already: ".$_GET['booked'].'.';
        echo '<br>';
    }
}
echo '</p>';

if($_GET['event_id']){
    echo '<h3>Event Information</h3>';
    $sql = "SELECT * FROM hlb_events WHERE (DATE(hlb_events.event_end_datetime) = DATE(NOW()) OR DATE(hlb_events.event_start_datetime) = DATE(NOW())) AND hlb_events.event_vis = 1 AND hlb_events.event_groups = 1 AND hlb_events.event_id = ".$_GET['event_id']." ORDER BY hlb_events.event_start_datetime ASC";
    $res = $con_sbr->query($sql);
    foreach($res as $val){
        $booked = explode(',',$val['crsid_booked']);
        $num = count($booked);
        if($val['crsid_booked']==''){
            $num = 0;
        }

        $checked_in = [];
        $sql_b = "SELECT * FROM hlb_events_groups WHERE hlb_events_groups.event_id = '".$val['event_id']."'";
        $res_b = $con_sbr->query($sql_b);
        foreach($res_b as $val_b){
            $check = explode(',',$val_b['group_booked']);
            foreach($check as $person){
                if(!in_array($person,$checked_in)){
                    $checked_in[] = $person;
                }
            }
        }
        

        if($val['event_sub']!=''){
            ?>
            <b>Event: </b><?php echo $val['event_name'].' - '.$val['event_sub'].' (Capacity: '.$num.'/'.$val['slots_max'].')';?>
            <?php
        }else{
            ?>
            <b>Event: </b><?php echo $val['event_name'].' (Capacity: '.$num.'/'.$val['slots_max'].')';?>
            <?php
        }
        ?>
        <br><b>Event Location: </b><?php echo $val['event_location'];?>
        <br><b>Event Start Date/Time: </b><?php echo $val['event_start_datetime'];?>
        <br><b>Checked In: </b><?php echo count($checked_in).'/'.$num;?>
        
        <form method='POST' action='form_send.php'>
        <input type='hidden' name='send_ref' value='groups_add'>
        <input type='hidden' name='event_id' value='<?php echo $val['event_id'];?>'>
        <input type='hidden' name='event_name' value='<?php echo $val['event_name'];?>'>
        <input type='text' name='group_list'>
        <input type='submit'>
        </form>
        
        <?php
        echo '<h3>Groups Checked-In</h3>';
        if(mysqli_num_rows($res_b)>0){
            $counter = 0;
            foreach($res_b as $val_b){
                $counter+=1;
                ?>
                <form method='POST' action='form_send.php'>
                <input type='hidden' name='send_ref' value='groups_del'>
                <input type='hidden' name='event_id' value='<?php echo $val['event_id'];?>'>
                <input type='hidden' name='event_name' value='<?php echo $val['event_name'];?>'>
                <input type='hidden' name='group_id' value='<?php echo $val_b['group_id'];?>'>
                <?php
                echo $counter.'. '.$val_b['group_booked'] ." (<input type='submit' class='link_submit' value='Delete'>)";
                echo '</form>';
            }
        }else{
            echo '<i>No groups exist (yet!).</i>';
        }
        
    }
    

}else{
    $sql = "SELECT * FROM hlb_events WHERE (DATE(hlb_events.event_end_datetime) = DATE(NOW()) OR DATE(hlb_events.event_start_datetime) = DATE(NOW())) AND hlb_events.event_vis = 1 AND hlb_events.event_groups = 1 ORDER BY hlb_events.event_start_datetime ASC";
    $res = $con_sbr->query($sql);
    echo '<div style="text-align: center">';
    if(mysqli_num_rows($res)==0){
        echo 'No (visable) events today!';
    }else{
        echo "<h3>Today's Events</h3>";
        foreach($res as $val){
            
            if($val['event_sub']){
                echo "<a href='groups?event_id=".$val['event_id']."'>".$val['event_name'].' - '.$val['event_sub'].'</a><br>';
            }else{
                echo "<a href='groups?event_id=".$val['event_id']."'>".$val['event_name'].'</a><br>';
            }
            echo '<br>';
            
        }
    }
    echo '</div>';
}


?>

<footer class="content-footer">
    <hr>
    <p>Created by <a href="https://www.lookup.cam.ac.uk/person/crsid/hlb54">Harry Bradshaw</a></p>
</footer>
</body>
</html>