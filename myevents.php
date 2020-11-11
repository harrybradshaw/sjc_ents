<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>sjcents</title>
    <link rel="stylesheet" href="templates/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat">
    <link rel="stylesheet" href="templates/assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="templates/assets/css/Footer-Dark.css">
    <link rel="stylesheet" href="templates/assets/css/styles.css">
</head>

<body>
<?php
include('db_connect.php');
//Remember to change back. Only serving events in the past for now...
$crsid = $_SERVER['REMOTE_USER'];
$user = '%'.$crsid.'%';
$sql_f = "SELECT * FROM hlb_events WHERE NOW() < event_end_datetime AND crsid_booked LIKE '".$user."' ORDER BY event_start_datetime"; 
$future = $con_sbr->query($sql_f);
$sql_p = "SELECT * FROM hlb_events WHERE NOW() > event_end_datetime AND crsid_booked LIKE '".$user."' ORDER BY event_start_datetime"; 
$past = $con_sbr->query($sql_p);
?>
    <nav class="navbar navbar-light navbar-expand-md sticky-top" style="background: linear-gradient(black, rgba(0,97,211,0.58) 0%);">
        <div class="container-fluid"><a class="navbar-brand" href="https://sjcents.com" style="font-family: Montserrat, sans-serif;color: rgba(255,255,255,0.9);">SJC Ents</a><button data-toggle="collapse" class="navbar-toggler" data-target="#navcol-1"><span class="sr-only">Toggle navigation</span><span class="navbar-toggler-icon"></span></button>
            <div
                class="collapse navbar-collapse" id="navcol-1">
                <ul class="nav navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link active" href="myevents" style="color: rgba(255,255,255,0.9);font-family: Montserrat, sans-serif;">My Events</a></li>
                    <li class="nav-item"><a class="nav-link active" href="#" style="color: rgba(255,255,255,0.9);font-family: Montserrat, sans-serif;">Logout CRSID</a></li>
                </ul>
        </div>
        </div>
    </nav>
    <header>
        <section style="background: linear-gradient(rgba(255,255,255,0) 59%, white), url(&quot;templates/assets/img/sjcents1.png&quot;), linear-gradient(rgba(0,97,211,0.58) 0%, white 74%);background-size: auto, auto, auto;height: 301px;"></section>
    </header>
    <main>
        <div class="container" style="padding-top: 25px;">
            <div class="row">
                <div class="col-md-10 col-lg-10 col-xl-10">
                    <section>
                        <h1 class="text-left" style="font-family: Montserrat, sans-serif;">My Events<br></h1>
                    </section>
                </div>
            </div>
        </div>
        <div class="container" style="padding-top: 20px;">
            <section class="text-left">
                <h3 class="text-left text-secondary" style="font-family: Montserrat, sans-serif;">Active</h3>
            </section>
            <?php if(mysqli_num_rows($future)>0) {
                ?>
                <div class="table-responsive" style="padding-top: 10px;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Event</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Location</th>
                                <th>Slots</th>
                                <th>Book</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach($future as $val){  
                                $booked = explode(',',$val['crsid_booked']);
                                if($val['crsid_booked']==''){
                                    $num = 0;
                            }else{
                                $num = count($booked);
                            } 
                        
                                echo '<tr>';
                                    echo '<td>'.$val['event_name'].'</td>';
                                    echo '<td>'.$val['event_start_datetime'].'</td>';
                                    echo '<td>'.$val['event_end_datetime'].'</td>';
                                    echo '<td>'.$val['event_location'].'</td>';
                                    echo '<td>'.$num.'/'.$val['slots_max'].'</td>';

                                    echo '<form class="modal-content" method="POST" action="form_send.php">';
                                    echo '<input type="hidden" name="send_ref" value="event_book">';
                                    echo '<input type="hidden" name="ref_page" value="myevents">';
                                    echo '<input type="hidden" name="event_id" value="'.$val['event_id'].'">';
                                    echo '<input type="hidden" name="event_name" value="'.$val['event_name'].'">';
                                    echo '<input type="hidden" name="event_bookee" value="'.$_SERVER['REMOTE_USER'].'">';
                                    echo '<input type="hidden" name="unbook" value="1">';
                                    echo '<td>';
                                    echo '<button type="submit" class="cancelbtn">Cancel</button>';
                                    echo '</td>';
                                    echo '</form>';
                                    
                                echo '</tr>';
                            
                            }

                            
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php
            }else{
                echo '<p style="font-family: Montserrat, sans-serif;">No future events found.</p>';
            }
            ?>
        </div>
        <div class="container">
            <div class="text-center"><a class="btn btn-primary btn-lg border-light" data-toggle="collapse" aria-expanded="false" aria-controls="collapse-1" href="#collapse-1" role="button" style="background: rgba(167,167,167,0.15);color: rgb(108,117,125);font-family: Montserrat, sans-serif;">Past Events</a>
                <div
                    class="collapse" id="collapse-1" style="padding-top: 25px;">
                    <section class="text-left">
                        <h3 class="text-left text-secondary" style="font-family: Montserrat, sans-serif;">Past</h3>
                    </section>
                    <div class="table-responsive" style="padding-top: 10px;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Event</th>
                                    <th>Start</th>
                                    <th>End</th>
                                    <th>Location</th>
                                    <th>Slots</th>
                                    <th>Book</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Cell 1</td>
                                    <td>Cell 2</td>
                                    <td>Cell 2</td>
                                    <td>Cell 2</td>
                                    <td>Cell 2</td>
                                    <td><button class="btn btn-primary border-light" type="button" style="background: rgb(204,31,31);">Book</button></td>
                                </tr>
                                <tr>
                                    <td>Cell 3</td>
                                    <td>Cell 4</td>
                                    <td>Cell 4</td>
                                    <td>Cell 4</td>
                                    <td>Cell 4</td>
                                    <td><button class="btn btn-primary border-light" type="button" style="background: rgb(204,31,31);">Book</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
            </div>
        </div>
        </div>
    </main>
    <div class="text-center footer-dark" style="margin-top: 25px;padding-top: 25px;padding-bottom: 25px;">
        <footer>
            <div class="container text-center" style="padding-right: 0px;padding-left: 0px;">
                <div class="row text-center">
                    <div class="col-3 col-xl-3 offset-1 offset-lg-2 offset-xl-2 text-right align-self-center"><a href="https://www.facebook.com/stjohnscollegejcr"><i class="fa fa-facebook-square" style="font-size: 60px;color: rgb(145,145,145);padding: 2px;"></i></a></div>
                    <div class="col-4 col-lg-2 col-xl-2 text-center"><a class="stretched-link" href="https://www.instagram.com/stjohnsjcr/"><i class="fa fa-instagram" style="font-size: 60px;color: rgb(145,145,145);padding: 2px;"></i></a></div>
                    <div class="col-3 col-xl-3 offset-0 text-left"><a href="https://www.snapchat.com/add/sjcjcr"><i class="fa fa-snapchat-ghost" style="font-size: 60px;color: rgb(145,145,145);padding: 2px;"></i></a></div>
                </div>
                <p class="copyright" style="font-size: 16px;">SJC JCR &amp; SBR Committees © 2020</p>
            </div>
        </footer>
    </div>
    <script src="templates/assets/js/jquery.min.js"></script>
    <script src="templates/assets/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>