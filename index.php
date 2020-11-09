<!DOCTYPE html>
<html>
<head>
<title>SJC Ents</title>
<link rel="stylesheet" href="style.css">
<link rel="icon" type="image/png" href="http://sbr.soc.srcf.net/booking/favicon.png"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>

<?php 
include 'components/topbar.php';
include 'db_connect.php';
include 'components/sidebar.php';

?>


<!-- Use any element to open the sidenav -->


<div class="main">
<div>
<h2>Your Upcoming Events</h2>

<?php
$sql = "SELECT * FROM hlb_events WHERE NOW() < event_end_datetime ORDER BY event_start_datetime"; 
$res = $con_sbr->query($sql);
$ye_head = true;

foreach($res as $val){
    $booked = explode(',',$val['crsid_booked']);
    
    $num = count($booked);
    if($val['crsid_booked']==''){
        $num = 0;
    }

    if(in_array($_SERVER['REMOTE_USER'],$booked)){
    ?>
    <div id="<?php echo 'id'.$val['event_id'].'c';?>" class="modal">
    
        <span onclick="document.getElementById('<?php echo 'id'.$val['event_id'].'c';?>').style.display='none'" class="close" title="Close Modal"></span>
        <form class="modal-content" method="POST" action="form_send.php">
            <?php
            echo '<input type="hidden" name="send_ref" value="event_book">';
            echo '<input type="hidden" name="event_id" value="'.$val['event_id'].'">';
            echo '<input type="hidden" name="event_name" value="'.$val['event_name'].'">';
            echo '<input type="hidden" name="event_bookee" value="'.$_SERVER['REMOTE_USER'].'">';
            echo '<input type="hidden" name="unbook" value="1">';
            
            ?>
            <div class="container">
            <h1>Confirm Cancellation</h1>
            <hr>
            <p><b>Event Name:</b> <?php echo $val['event_name'];if($val['event_sub']!=''){echo ' - '.$val['event_sub'];}?> <br>
            <b>Location:</b> <?php echo $val['event_location'];?> <br>
            <?php
            if($val['event_desc']!=''){
                ?>
                <p><b>Event Description:</b> <?php echo $val['event_desc'];?> <br>
                <?php
            }
            ?>
            <br>
            <b>Date/Time:</b> <? echo $val['event_start_datetime'];?><br>
            </p>
            <div class="clearfix">
                <button type="button" onclick="document.getElementById('<?php echo 'id'.$val['event_id'].'c';?>').style.display='none'" class="signupbtn">Undo</button>
                <button type="submit" class="cancelbtn">Cancel Booking</button>
            </div>
            </div>
        </form>
    </div>

    <?php
    }
}
    
foreach($res as $val){
    if($val['slots_vis']){
        $booked = explode(',',$val['vis_crsid_booked']);
    }else{
        $booked = explode(',',$val['crsid_booked']);
    }
    
    $num = count($booked);
        if($val['crsid_booked']==''){
            $num = 0;
        }

    if(in_array($_SERVER['REMOTE_USER'],$booked)){

        if($ye_head){
            echo '<table id = "customers"><th>Event Name</th><th>Date/Time</th><th>Location</th><th>Slots Booked</th>';
            $ye_head = false;
        }
        echo '<tr>';
        echo '<form method="post" action="form_send.php">';
        echo '<input type="hidden" name="send_ref" value="event_book">';
        echo '<input type="hidden" name="event_id" value="'.$val['event_id'].'">';
        echo '<input type="hidden" name="event_name" value="'.$val['event_name'].'">';
        echo '<input type="hidden" name="event_bookee" value="'.$_SERVER['REMOTE_USER'].'">';
        echo '<td>'.$val['event_name'];
        if($val['event_sub']!=''){
            echo ' - '.$val['event_sub'];
        }
        echo '</td>';
        echo '<td>'.$val['event_start_datetime'].'</td>';
        echo '<td>'.$val['event_location'].'</td>';
        if($val['slots_vis']){
            echo '<td>'.count(explode(',',$val['crsid_booked'])).'/'.$val['slots_vis'].'</td>';
        }else{ 
            echo '<td>'.$num.'/'.$val['slots_max'].'</td>';
        }
        
        echo '<td>';
        ?>
        </form>
        <?php
        $bad = false;
        $date1 = strtotime('+3 hours',strtotime(date("Y-m-d H:i:s")));
        $date2 = strtotime($val['event_start_datetime']);
        if( $date1 > $date2){
            $bad = true;
        }
        if($bad){
            ?>
            <button class="unavbtn" disabled>Booking Closed</button>
            <?php
        }else{
            ?>
            <button class="unbookbtn" onclick="document.getElementById('<?php echo 'id'.$val['event_id'].'c'?>').style.display='block'">Cancel Booking</button>
            <?php
        } 
        ?>
        </td>
        </tr>
        <?php
    
    }   
}
if(!$ye_head){
    echo '</table>';
}else{
    ?>
    <p> You are not currently booked onto any events. </p>
    <?php
}

?>
</div>

<div>
<h2>All Events</h2>
<?php
$sql = "SELECT * FROM hlb_events WHERE NOW() < event_start_datetime AND event_vis = 1 ORDER BY event_start_datetime"; 
$res = $con_sbr->query($sql);
$dupes = [];
$dupes2 = [];
//Generate Modals
foreach($res as $val){
    if(!in_array($val['event_id'],$dupes)){
        //Find all other occurances of the same event. 
        $sql_b = "SELECT * FROM hlb_events WHERE event_name = '".$val['event_name']."' AND NOW() < event_start_datetime AND event_vis = 1 ORDER BY event_start_datetime";
        $res_b = $con_sbr->query($sql_b);
        if(mysqli_num_rows($res_b)>1){
            //Then we have multiple instances.
            foreach($res_b as $val_b){
                if($val_b['event_stack']){
                    if($val_b['event_id'] != $val['event_id']){
                        $dupes[] = $val_b['event_id'];
                    }   
                }
                
            }
        }else{
            //Only occurance.
        }
        
        $booked = explode(',',$val['crsid_booked']);
        
        $num = count($booked);
        if($val['crsid_booked']==''){
            $num = 0;
        }
        ?>
        <div id="<?php echo 'id'.$val['event_id'].'b';?>" class="modal">
        
            <span onclick="document.getElementById('<?php echo 'id'.$val['event_id'].'b';?>').style.display='none'" class="close" title="Close Modal"></span>
            <form class="modal-content" method="POST" action="form_send.php">
                <?php
                
                echo '<input type="hidden" name="event_name" value="'.$val['event_name'].'">';
                if(mysqli_num_rows($res_b)==1){
                echo '<input type="hidden" name="event_id" value="'.$val['event_id'].'">';
                }
                if($val['event_gallocation']){
                    echo '<input type="hidden" name="send_ref" value="event_book_allocation">';
                }else{
                    echo '<input type="hidden" name="send_ref" value="event_book">';
                }
                echo '<input type="hidden" name="event_bookee" value="'.$_SERVER['REMOTE_USER'].'">';
                echo '<input type="hidden" name="book" value="1">';
                
                ?>
                <div class="container">
                <h1>Confirm Booking</h1>
                <hr>
                <p><b>Event Name:</b> <?php echo $val['event_name'];?> <br>
                <b>Location:</b> <?php echo $val['event_location'];?> <br>
                
                <?php
                if($val['event_desc']!=''){
                    ?>
                    <p><b>Event Description:</b> <?php echo $val['event_desc'];?> <br>
                    <?php
                }
                
                if(mysqli_num_rows($res_b)>1){
                    ?>
                    <br>
                    <b>Select a Date/Time:</b><br>
                    <?php
                    $count_ops = 0;
                    foreach($res_b as $val_b){
                        $booked_b = explode(',',$val_b['crsid_booked']);
                        $num_b = count($booked_b);
                        if($val_b['crsid_booked']==''){
                            $num_b = 0;
                        }
                        //Check there are open slots.
                        if($val_b['slots_vis']){
                            $slots = $val_b['slots_vis'];
                        }else{
                            $slots = $val_b['slots_max'];
                        }

                        if($val_b['event_open']==1){
                            if($num_b < $slots){
                                //proceed
                                $bad = false;
                                $date1 = strtotime('+3 hours',strtotime(date("Y-m-d H:i:s")));
                                $date2 = strtotime($val_b['event_start_datetime']);
                                if( $date1 > $date2){
                                    $count_ops +=1;
                                    ?>
                                    <input type="radio" id="<?php echo $val_b['event_id'];?>" name="event_id" value="<?php echo $val_b['event_id'];?>"disabled >
                                    <label for="<?php echo $val_b['event_id'];?>"><?php echo $val_b['event_start_datetime'];if($val_b['event_sub']!=''){echo ' - '.$val_b['event_sub'];} echo' ('.$num_b.'/'.$val_b['slots_max'].' Booked) - ';?><b><i>Booking Closed</i></b></label><br>
                                    <?php
                                    
                                }else{
                                    if(!in_array($_SERVER['REMOTE_USER'],$booked_b)){
                                        $count_ops +=1;
                                        ?>
                                        <input type="radio" id="<?php echo $val_b['event_id'];?>" name="event_id" value="<?php echo $val_b['event_id'];?>" >
                                        
                                        <label for="<?php echo $val_b['event_id'];?>"><?php echo $val_b['event_start_datetime'];if($val_b['event_sub']!=''){echo ' - '.$val_b['event_sub'];} echo' ('.$num_b.'/'.$val_b['slots_max'].' Booked)';?></label><br>
                                        <?php
                                    }
                                }
                            }else{
                                //will display option but is not selectable.
                                if(!in_array($_SERVER['REMOTE_USER'],$booked_b)){
                                    $count_ops +=1;
                                    ?>
                                    <input type="radio" id="<?php echo $val_b['event_id'];?>" name="event_id" value="<?php echo $val_b['event_id'];?>"disabled >
                                    <label for="<?php echo $val_b['event_id'];?>"><?php echo $val_b['event_start_datetime'];if($val_b['event_sub']!=''){echo ' - '.$val_b['event_sub'];} echo' ('.$num_b.'/'.$val_b['slots_max'].' Booked) - ';?><b><i>Event Full</i></b></label><br>
                                    <?php
                                }
    
                            }
                        }else{
                            if(!in_array($_SERVER['REMOTE_USER'],$booked_b)){
                                $count_ops +=1;
                                ?>
                                <input type="radio" id="<?php echo $val_b['event_id'];?>" name="event_id" value="<?php echo $val_b['event_id'];?>" disabled >
                                
                                <label for="<?php echo $val_b['event_id'];?>"><?php echo $val_b['event_start_datetime'];if($val_b['event_sub']!=''){echo ' - '.$val_b['event_sub'];} echo' ('.$num_b.'/'.$val_b['slots_max'].' Booked) - ';?><b><i>Currently Unavailable to Book</i></b></label><br>
                                <?php
                            }
                        }
                        
                        
                        
                        
                    }
                    if($count_ops ==0){
                        ?>
                        <i>There are no more options available at this time.</i><br>
                        <?php
                    }
                }else{
                    echo '<br><b>Time:</b> '.$val['event_start_datetime'].'<br>';
                }
                ?>
                </p>
                <div class="clearfix">
                    <button type="button" onclick="document.getElementById('<?php echo 'id'.$val['event_id'].'b';?>').style.display='none'" class="cancelbtn">Cancel</button>
                    <button type="submit" class="signupbtn">Book</button>
                </div>
                </div>
            </form>
        </div>
        
        <?php
    }
}

//Start the table and fill...
if(mysqli_num_rows($res)>0){
    echo '<table id = "customers"><th>Event Name</th><th>Date/Time</th><th>Location</th><th>Slots Booked</th>';
}

$date1 = strtotime('+3 hours',strtotime(date("Y-m-d H:i:s")));
foreach($res as $val){
    if(!in_array($val['event_id'],$dupes)){
        //Find all other occurances of the same event. 
        $sql_b = "SELECT * FROM hlb_events WHERE event_name = '".$val['event_name']."' AND NOW() < event_start_datetime AND event_vis = 1";
        $res_b = $con_sbr->query($sql_b);
        if(mysqli_num_rows($res_b)>1){
            //Then we have multiple instances.
            foreach($res_b as $val_b){
                if($val_b['event_stack']){
                    if($val_b['event_id'] != $val['event_id']){
                        $dupes[] = $val_b['event_id'];
                    }   
                }
            }
        }else{
            //Only occurance.
        }
        $bad = false;
        $can_id = -1;
        $bad_res ='';
        $date2 = strtotime($val['event_start_datetime']);
        if(mysqli_num_rows($res_b)>1){

        }else{
            if( $date1 > $date2){
                $bad = true;
                $bad_res = 'Booking Closed';
            } 
        }
        
        
        echo '<tr>';
        echo '<td>'.$val['event_name'].'</td>';
        if(mysqli_num_rows($res_b)>1){
            echo '<td>Various Times</td>';
        }else{
            echo '<td>'.$val['event_start_datetime'].'</td>';
        }
        
        echo '<td>'.$val['event_location'].'</td>';
        
        $booked = explode(',',$val['crsid_booked']);
        
        $num = count($booked);
        if($val['crsid_booked']==''){
            $num = 0;
        }
        if(mysqli_num_rows($res_b)>1){
            echo '<td>See Individual</td>';
        }else{
            if($val['slots_vis']){
                echo '<td>'.count(explode(',',$val['crsid_booked'])).'/'.$val['slots_vis'].'</td>';
            }else{
                echo '<td>'.$num.'/'.$val['slots_max'].'</td>';
            }
            
        }
        
        echo '<td>';
        
        //Check if we have events that need stacking. 
        if(mysqli_num_rows($res_b)>1){
            //Lets check the avilability of all those other events.
            $all_full = 1;
            $all_expired = 1;
            foreach($res_b as $val_b){
                $booked_b = explode(',',$val_b['crsid_booked']);
                $num_b = count($booked_b);
                if($val_b['crsid_booked']==''){
                    $num_b = 0;
                }
                if($num_b < $val_b['slots_max']){
                    $all_full = 0;
                }
                $date2 = strtotime($val_b['event_start_datetime']);
                if( $date1 < $date2){
                    $all_expired = 0;
                }
            }

            //There are multiple events here that cannot both be attended. 
            if($val['event_style_id'] >0){
                
                //Now check if booked onto other versions of same event, where they are not permitted so book another space. In hindsight, a hilariously inefficent way of doing this...
                $sql_a = "SELECT * FROM hlb_events WHERE event_style_id = ".$val['event_style_id'];
                $res_a = $con_sbr->query($sql_a);
                foreach($res_a as $val_a){
                    $booked_a = explode(',',$val_a['crsid_booked']);
                    if(in_array($_SERVER['REMOTE_USER'],$booked_a)){
                        $bad = true;
                        if($val_a['event_name']==$val['event_name']){
                            $can_id = $val_a['event_id'];
                        }
                    }
                }   
            }

            if($bad){
                //echo '<input type="submit" name="book" value="Unavailable" disabled>';
                if($can_id>0){
                    ?>
                    <button class="unbookbtn" onclick="document.getElementById('<?php echo 'id'.$can_id.'c'?>').style.display='block'">Cancel Booking</button>
                    <?php
                }
                elseif($bad_res==''){
                    ?>
                    <button class="unavbtn" disabled>Unavailable</button>
                    <?php

                }else{
                    echo '<button class="unavbtn" disabled>'.$bad_res.'</button>';
                }
                
                
            }else{
                if($all_full){
                    ?>
                    <button class="unavbtn" disabled>Event Full</button>
                    <?php
                }elseif($all_expired){
                    ?>
                    <button class="unavbtn" disabled>Booking Closed</button>
                    <?php
                }else{
                    ?>
                    <button onclick="document.getElementById('<?php echo 'id'.$val['event_id'].'b'?>').style.display='block'">Book a Slot</button>
                    <?php
                }
                
            }
                
        //Only dealing with the one instance. 
        }else{
            if($val['event_open']==0){
                $bad = true;
            }
            //If they have already booked into the event.
            if($val['slots_vis']){
                $slots = $val['slots_vis'];
            }else{
                $slots = $val['slots_max'];
            }
            $booked = explode(',',$val['crsid_booked']);
            $num = count($booked);
            if(in_array($_SERVER['REMOTE_USER'],$booked)){
                if($bad){
                    ?>
                    <button class="unavbtn" disabled>Booking Closed</button>
                    <?php
                }else{
                    ?>
                    <button class="unbookbtn" onclick="document.getElementById('<?php echo 'id'.$val['event_id'].'c'?>').style.display='block'">Cancel Booking</button>
                    <?php
                } 
            
            }elseif($num < $slots){
                if($val['event_style_id'] >0){
                    //now check if booked onto other versions of same event.
                    $sql_a = "SELECT * FROM hlb_events WHERE event_style_id = ".$val['event_style_id'];
                    $res_a = $con_sbr->query($sql_a);
                    foreach($res_a as $val_a){
                        $booked_a = explode(',',$val_a['crsid_booked']);
                        if(in_array($_SERVER['REMOTE_USER'],$booked_a)){
                            $bad = true;
                        }
                    }
                    
                }
                if($bad){
                    //echo '<input type="submit" name="book" value="Unavailable" disabled>';
                    if($bad_res==''){
                        ?>
                        <button class="unavbtn" disabled>Unavailable</button>
                        <?php
        
                    }else{
                        echo '<button class="unavbtn" disabled>'.$bad_res.'</button>';
                    }
                }else{
                    //echo '<input type="submit" name="book" value="Book a Slot">';
                    ?>
                    <button onclick="document.getElementById('<?php echo 'id'.$val['event_id'].'b'?>').style.display='block'">Book a Slot</button>
                    <?php
                }
                
            }else{
                //echo '<input type="submit" name="full" value="Event Full" disabled>';
                ?>
                <button class="unavbtn" disabled>Event Full</button>
                <?php
            
            }
        }
        
        echo'</td>';
        echo '</tr>';
    }
    
}
if(mysqli_num_rows($res)>0){
    echo '</table>';
}else{
    echo '<p>There are no future events available at this time.</p>';
}

?>
</div>
<h2>Additional Information</h2>
<button type="button" class="collapsible"><b>Booking Information</b></button>
<div class="content">
<p>Thank you for your interest in the SBR event calendar - by signing up to an event, you accept the below:</p>
<ul>
<li>Only members of the College are allowed to participate in the event.</li>
<li>We encourage households to sign up to events together (each member of the household must still sign up using their individual CRSid).</li>
<li>If you are unable to attend an event, please log onto the webpage and cancel your ticket. Numbers are strictly limited and we reserve the right to exclude people from future events if in the event of a no-show.</li>
<li>The CRSids of participants at SBR events are recorded to help with contact tracing in the event of a local Covid-19 outbreak.</li>
<li>In keeping with current Government guidelines, all events will take place in groups of six. No mingling between the groups is allowed and offenders may be removed from the event and banned from future events.</li>
<li>Toilet facilities may be limited depending on the location of the event - please take this into consideration when booking.</li>
<li>Members of the SBR Committee will be present at all events if there are any queries, and to remind participants of the regulations. They will be easily identifiable, most likely wearing some SBR 'stash'.</li>
<li>No gowns necessary for any of the events!</li>
</ul>
<p>
Face masks are mandatory on College grounds (unless you are exempt) and are only allowed to be taken off when drinking/eating while maintaining social distancing. If you have to take off your face mask, please ensure sure you maintain an appropriate distance to others.
Sanitise hands before and after the event (we will provide sanitising stations but encourage you to have your own sanitiser gel with you at all times).
<br>
<br>The booking/cancellation window closes three hours before each event.
<br>
<br>
For any technical questions regarding the website/booking system, please contact our webmaster <a href="mailto:hlb54@cam.ac.uk">Harry</a>. 
</p>
</div>

<button type="button" class="collapsible"><b>Health Disclaimer</b></button>
<div class="content">
    <p>In order to create safe and enjoyable environments for participants in SBR events, we are asking all attendees to acknowledge the following self-declaration prior to attending any SBR event. 
    This and other adaptations are for your benefit and to protect a large number of people from having to self-isolate in the event that someone subsequently becomes symptomatic. 
    <b>By booking onto an event, you declare the following:</b>
    <ul>
    <li>In the last 7 days, I have not displayed any of the below symptoms:<br>
    - Fever <br>
    - Continuous cough<br>
    - A loss of, or change in, my normal sense of taste or smell. 
    </li><br>
    <li> To the best of my knowledge, I have not been in contact with anyone infected, suspected or diagnosed, with COVID-19 within the past two weeks. 

    (contact refers to a distance of less than 2 metres, for longer than 15 minutes in the absence of protective face coverings)
    </li><br>
    <li>I agree to abide by both College and local guidance in relation to the delivery of the event, such as the use of PPE. </li><br>
    <li>I agree to follow the College guidelines and Public Health England guidelines in relation to hygiene, handwashing, social distancing for the duration of the event (including national guidelines on transport to and from the event). </li><br>
    <li>I agree that if any of the above changes within 24 hours of the event, I will notify the event organiser to discuss my circumstances. </li><br>
    </ul>    
    </p>
</div>


<footer class="content-footer">
    <hr>
    <p>Created by <a href="https://www.lookup.cam.ac.uk/person/crsid/hlb54">Harry Bradshaw</a></p>
</footer>

</div>
<script>
var coll = document.getElementsByClassName("collapsible");
var i;

for (i = 0; i < coll.length; i++) {
  coll[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var content = this.nextElementSibling;
    if (content.style.display === "block") {
      content.style.display = "none";
    } else {
      content.style.display = "block";
    }
  });
}

</script>
</body>
</html>