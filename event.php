<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>SJC Ents - Event</title>
    <link rel="stylesheet" href="templates/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat">
    <link rel="stylesheet" href="templates/assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="templates/assets/css/Footer-Dark.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.2.0/aos.css">
    <link rel="stylesheet" href="templates/assets/css/styles.css">
</head>

<body>
<nav class="navbar navbar-light navbar-expand-md sticky-top" style="background: linear-gradient(black, rgba(0,97,211,0.58) 0%);">
        <div class="container-fluid"><a class="navbar-brand" href="#" style="font-family: Montserrat, sans-serif;color: rgba(255,255,255,0.9);">SJC Ents</a><button data-toggle="collapse" class="navbar-toggler" data-target="#navcol-1"><span class="sr-only">Toggle navigation</span><span class="navbar-toggler-icon"></span></button>
            <div
                class="collapse navbar-collapse" id="navcol-1">
                <ul class="nav navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link active" href="#" style="color: rgba(255,255,255,0.9);font-family: Montserrat, sans-serif;">My Events</a></li>
                    <li class="nav-item"><a class="nav-link active" href="#" style="color: rgba(255,255,255,0.9);font-family: Montserrat, sans-serif;">Logout CRSID</a></li>
                </ul>
        </div>
        </div>
    </nav>
<header>
        <section style="background: linear-gradient(rgba(255,255,255,0) 59%, white), url(&quot;templates/assets/img/sjcents1.png&quot;), linear-gradient(rgba(0,97,211,0.58) 0%, white 74%);background-size: auto, auto, auto;height: 301px;"></section>
    </header>
    <main>
    <?php
        include('db_connect.php');
        if($_GET['id']){
            $sql = "SELECT * FROM hlb_events WHERE NOW() < event_end_datetime AND event_id = ".$_GET['id']." ORDER BY event_start_datetime"; 
            $res = $con_sbr->query($sql);
            foreach($res as $val){

        }
    ?>
        <div class="container" style="padding-top: 25px;">
        <div class="row">
                <div class="col-md-10 col-lg-10 col-xl-10">
            <section>
                <?php
                echo '<h1 class="text-left" style="font-family: Montserrat, sans-serif;">'.$val['event_name'].'<br></h1>';
                echo '<h3 class="text-left text-secondary" style="font-family: Montserrat, sans-serif;">'.$val['event_sub'].'</h3>';
                ?>
            </section>
            </div>
                <div class="col-md-2 col-lg-2 col-xl-2 text-left"><button class="btn btn-primary border-light" type="button" style="background: rgb(204,31,31);" onclick="location.href='./'">Go Back</button></div>
            </div>
            <div class="row" style="padding-top: 20px;">
                <div class="col-sm-12 col-lg-6 col-xl-5">
                <?php if($val['event_imgsrc']){
                   echo '<div class="card" style="border-style: none;"><img class="img-fluid card-img-top w-100 d-block border rounded" src="'.$val['event_imgsrc'].'" style="box-shadow: 0px 0px 10px 0px;">';
                      
                    } 
                    ?>
                    <?php if($val['event_fblink']){
                    
                    echo '<div class="card-body" style="text-align: center;"><button class="btn btn-primary" type="button" style="background: rgb(204,31,31);" onclick="location.href=\''.$val['event_fblink'].'\'">Facebook Event</button></div>';
                        
                    } 
                    ?>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col text-center">
                    <p class="text-left" style="padding: 5px;font-family: Montserrat, sans-serif;">
                    <?php
                        echo nl2br($val['event_desc']);
                    ?>
                    </p>    
                </div>
            </div>
        </div>
        <br>
        <div class="container">
            <section class="text-left">
                <h2 class="text-left text-black-50" style="/*background-image: linear-gradient(to bottom , #ececec, white);*/font-family: Montserrat, sans-serif;">Event Info</h2>
                <?php
                if($val['event_vis']){
                    $sql_b = "SELECT * FROM hlb_events WHERE event_name = '".addslashes($val["event_name"])."' AND NOW() < event_end_datetime AND event_vis=1";
                    $res_b = $con_sbr->query($sql_b);
                    foreach($res_b as $val_b){
                        if($val_b['event_open']){
                            $booking_flag = true;
                        }
                        elseif($val_b['event_reglink']){
                            $reglink_flag = true;
                        }
                    }
                    ?>
                    <div class="table-responsive" style="padding-top: 10px;">
                    <table class="table">
                        <tr>
                            <?php
                                if($val['event_subname']){
                                    echo '<th>Name</th>';
                                }
                            ?>
                            <th>Location</th>
                            <th>Event Start</th>
                            <th>Event End</th>
                            <?php if ($booking_flag){
                            echo '<th>Slots</th>';
                            echo '<th></th>';
                            }elseif($reglink_flag){
                                echo '<th></th>';
                            }
                            ?>
                        </tr>
                    <?php
                    foreach($res_b as $val_b){
                        $booked = explode(',',$val_b['crsid_booked']);
                        if($val_b['crsid_booked']==''){
                            $num = 0;
                        }else{
                            $num = count($booked);
                        }
                        
                        echo '<tr>';
                        if($val_b['event_subname']){
                            echo "<td>".addslashes($val_b["event_subname"])."</td>";
                        }
                        if($val_b['event_location']){
                            echo '<td>'.$val_b["event_location"].'</td>';
                        }else{
                            echo '<td>TBC</td>';
                        }
                        echo '<td>'. $val_b['event_start_datetime'] .'</td>';
                        echo '<td>'. $val_b['event_end_datetime'] .'</td>';
                        if ($val_b['event_open']){
                        echo '<td>'. $num.'/'.$val_b['slots_max'] .'</td>';
                        echo '<td>';

                        if(in_array($_SERVER['REMOTE_USER'],$booked)){
                            echo '<form class="modal-content" method="POST" action="form_send.php">';
                            echo '<input type="hidden" name="send_ref" value="event_book">';
                            echo '<input type="hidden" name="ref_page" value="'.$_GET['id'].'">';
                            echo '<input type="hidden" name="event_id" value="'.$val_b['event_id'].'">';
                            echo '<input type="hidden" name="event_name" value="'.$val_b['event_name'].'">';
                            echo '<input type="hidden" name="event_bookee" value="'.$_SERVER['REMOTE_USER'].'">';
                            echo '<input type="hidden" name="unbook" value="1">';
                            ?>
                            <button type="submit" class="cancelbtn">Cancel</button>
                            </form>
                            <?php

                        }else{
                            echo '<form class="modal-content" method="POST" action="form_send.php">';
                            echo '<input type="hidden" name="send_ref" value="event_book">';
                            echo '<input type="hidden" name="event_id" value="'.$val_b['event_id'].'">';
                            echo '<input type="hidden" name="ref_page" value="'.$_GET['id'].'">';
                            echo '<input type="hidden" name="event_name" value="'.$val_b['event_name'].'">';
                            echo '<input type="hidden" name="event_bookee" value="'.$_SERVER['REMOTE_USER'].'">';
                            echo '<input type="hidden" name="book" value="1">';
                            ?>
                            <button type="submit" class="bkbutton">Book</button>
                            </form>
                            <?php
                        }
                        
                        echo '</td>';
                        }elseif($booking_flag){
                            echo '<td></td>';
                            echo '<td></td>';
                        }elseif($val_b['event_reglink']){
                            echo '<td>';
                            echo '<button type="submit" class="bkbutton" onclick="location.href=\''.$val['event_reglink'].'\'">Register</button>';
                            echo '</td>';
                        }elseif($reglink_flag){
                            echo '<td></td>';
                        }
                        echo '</tr>';
                    }
                    ?>
                    </table>
                    </div>
                    <?php
                }else{
                    echo '<p style="padding: 5px;font-family: Montserrat, sans-serif;">This event is unable to be booked at this point in time.</p>';
                }
                ?>
            </section>
        </div>
        <?php
        }
        ?>
    </main>
    <div class="text-center footer-dark" style="margin-top: 25px;padding-top: 25px;padding-bottom: 25px;">
        <footer>
            <div class="container text-center" style="padding-right: 0px;padding-left: 0px;">
                <div class="row text-center">
                    <div class="col-3 col-xl-3 offset-1 offset-lg-2 offset-xl-2 text-right align-self-center" data-aos="fade-right"><a href="https://www.facebook.com/stjohnscollegejcr"><i class="fa fa-facebook-square" style="font-size: 60px;color: rgb(145,145,145);padding: 2px;"></i></a></div>
                    <div class="col-4 col-lg-2 col-xl-2 text-center" data-aos="fade"><a class="stretched-link" href="https://www.instagram.com/stjohnsjcr/"><i class="fa fa-instagram" style="font-size: 60px;color: rgb(145,145,145);padding: 2px;"></i></a></div>
                    <div class="col-3 col-xl-3 offset-0 text-left" data-aos="fade-left"><a href="https://www.snapchat.com/add/sjcjcr"><i class="fa fa-snapchat-ghost" style="font-size: 60px;color: rgb(145,145,145);padding: 2px;"></i></a></div>
                </div>
                <p class="copyright" style="font-size: 16px;">SJC JCR &amp; SBR Committees © 2020</p>
            </div>
        </footer>
    </div>
    <script src="templates/assets/js/jquery.min.js"></script>
    <script src="templates/assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="templates/assets/js/bs-init.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.2.0/aos.js"></script>
</body>

</html>