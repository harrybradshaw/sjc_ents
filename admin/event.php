<!DOCTYPE html>
<html>

<head>
  <title>SJC Ents - Add Event</title>
  <link rel="icon" type="image/png" href="http://sbr.soc.srcf.net/booking/favicon.png"/>
<link rel="stylesheet" href="../style.css">
</head>

<body>
  <table style="border-collapse: collapse; width:100%;height:39px;">
    <tr style="border-bottom: 2px solid #dddddd;text-align: center;border-top: 2px solid #dddddd;">
    <th> <a href="./">Event View</a> </th>
    <th style="font-size:16pt; width:20%;font-family:Calibri,Helvetica, Arial, sans-serif;">SJC Ents - Admin</th>
    <th> <a href="?view=crsid">CRSID View </th>
    </tr>
    </table>
    

<br>

<?php if(in_array($_SERVER['REMOTE_USER'],['hlb54','sp770','dw545','hm404','cmfg2','mms72','ms2340','ojrb2','djr76','lcm67'])){
  include '../db_connect.php';
  if(!($_GET['id'])){
    ?>
    <h2>Add event</h2>
    <div>
<form method="post" action="../form_send.php" >
    <input type="hidden" name="send_ref" value="event_create">
    <label for="event_name">Event Name:</label><br>
    <input type="text" name="event_name" required>
    <br>
    <label for="event_sub">Event Subtitle: </label><br>
    <input type="text" id = "event_sub" name="event_sub">
    <br>
    <label for="event_desc">Event Description: </label><br>
    <textarea id = "event_desc" name="event_desc" style="margin: 0px;width: 1802px;height: 147px;"></textarea><br>
    <label for="event_location">Event Location: </label><br>
    <input type="text" id = "event_location" name="event_location" value="TBC">
    <br>
    <label for="slots_max">Maximum Booking Slots: </label><br>
    <input type="number" name="slots_max" min="0">
    <br><br>
    <label for="event_start_datetime">Event Start:</label><br>
    <input type="datetime-local" id="event_start_datetime" name="event_start_datetime" required><br>
    <label for="event_end_datetime">Event End:</label><br>
    <input type="datetime-local" id="event_end_datetime" name="event_end_datetime" required><br><br>
    <label for="event_vis">Event Visible:</label>
    <input type="checkbox" name="event_vis"><br>
    <label for="event_open">Event open for booking:</label>
    <input type="checkbox" name="event_open"><br><br>
    <label for="event_fblink">Facebook Link: </label><br>
    <input type="text" id = "event_fblink" name="event_fblink" value=''>
    <br>
    <label for="event_zoomlink">Zoom Link: </label><br>
    <input type="text" id = "event_zoomlink" name="event_zoomlink" value=''>
    <br>
    <label for="event_reglink">External Registration Link: </label><br>
    <input type="text" id = "event_reglink" name="event_reglink" value=''>
    <br>
    <label for="event_imgsrc">Img Src: </label><br>
    <input type="text" id = "event_imgsrc" name="event_imgsrc" value=''>
    <br>
    
    <br>
    <button type="submit" value="Submit">Add Event</button>
  </form>
</div>

    <?php

  }else{
    $sql = "SELECT * FROM hlb_events WHERE event_id = ".$_GET['id'];
    $res = $con_sbr->query($sql);
    foreach($res as $val){
    ?>
    <h2>Edit Event</h2>
    <div>
  <form method="post" action="../form_send.php" >
    <input type="hidden" name="send_ref" value="event_edit">
    <?php echo '<input type="hidden" name="event_id" value="'.$_GET['id'].'">' ?>
    <label for="event_name">Event Name:</label><br>
    <?php echo '<input type="text" name="event_name" required value="'.$val['event_name'].'">' ?>
    <br>
    <label for="event_sub">Event Subtitle: </label><br>
    <?php echo '<input type="text" id = "event_sub" name="event_sub" value="'.$val['event_sub'].'">' ?>
    <br>
    <label for="event_subname">Event Subname </label><br>
    <?php echo '<input type="text" id = "event_subname" name="event_subname" value="'.$val['event_subname'].'">' ?>
    <br>
    <label for="event_desc">Event Description: </label><br>
    <?php echo '<textarea id = "event_desc" name="event_desc" style="margin: 0px;width: 100%;height: 147px;">'.$val['event_desc'].'</textarea>'?>
    <br>
    <label for="event_location">Event Location: </label><br>
    <?php echo '<input type="text" id = "event_location" name="event_location" value="'.$val['event_location'].'">' ?>
    <br>
    <label for="slots_max">Maximum Booking Slots: </label><br>
    <?php echo '<input type="number" name="slots_max" min="0" value="'.$val['slots_max'].'">'?>
    <br><br>
    <label for="event_start_datetime">Event Start:</label><br>
    <?php 
    $new_list = explode(' ',$val['event_start_datetime']);
    $new_string = $new_list[0].'T'.$new_list[1];
    echo '<input type="datetime-local" id="event_start_datetime" name="event_start_datetime" value="'.$new_string.'" required><br>' ?>
    <label for="event_end_datetime">Event End:</label><br>
    <?php 
    $new_list = explode(' ',$val['event_end_datetime']);
    $new_string = $new_list[0].'T'.$new_list[1];
    echo '<input type="datetime-local" id="event_end_datetime" name="event_end_datetime" value="'.$new_string.'" required><br><br>' ?>
    <label for="event_vis">Event Visible:</label>
    <?php if($val['event_vis']){
       echo '<input type="checkbox" name="event_vis" checked><br>';
    }else{
      echo '<input type="checkbox" name="event_vis"><br>';
    }
     
    ?>
    <label for="event_open">Event open for booking:</label>
    <?php if($val['event_open']){
      echo ' <input type="checkbox" name="event_open" checked><br><br>';
    }else{
      echo '<input type="checkbox" name="event_open"><br><br>';
    }
   
    ?>
    <label for="event_fblink">Facebook Link: </label><br>
    <?php echo '<input type="text" id = "event_fblink" name="event_fblink" value="'.$val['event_fblink'].'">' ?>
    <br>
    <label for="event_zoomlink">Zoom Link: </label><br>
    <?php echo '<input type="text" id = "event_zoomlink" name="event_zoomlink" value="'.$val['event_zoomlink'].'">' ?>
    <br>
    <label for="event_reglink">External Registration Link: </label><br>
    <?php echo '<input type="text" id = "event_reglink" name="event_reglink" value="'.$val['event_reglink'].'">' ?>
    <br>
    <label for="event_imgsrc">Img Src: </label><br>
    <?php echo '<input type="text" id = "event_imgsrc" name="event_imgsrc" value="'.$val['event_imgsrc'].'">' ?>
    <br>
    
    <br>
    <button type="submit" value="Submit">Submit Edits</button>
  </form>
</div>
<?php
    }
  }
  ?>

<?php
}else{
  header('location:../');
}
?>
</body>
</html>