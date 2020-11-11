<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>SJC Ents</title>
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
        <section style="background: url(&quot;templates/assets/img/sjcents1.png&quot;), linear-gradient(rgba(0,97,211,0.58) 0%, white 93%);background-size: auto, auto;height: 450px;"></section>
    </header>
    <?php
    //Grab all the relevant events from the DB.
    include('db_connect.php');
    //Remember to change back. Only serving events in the past for now...
    $sql = "SELECT * FROM hlb_events WHERE NOW() < event_end_datetime ORDER BY event_start_datetime"; 
    $res = $con_sbr->query($sql);
    
    function CreateEvent($event_id) {
        include('db_connect.php');
        $sql = "SELECT * FROM hlb_events WHERE NOW() < event_end_datetime AND event_id=".$event_id." ORDER BY event_start_datetime"; 
        $res = $con_sbr->query($sql);
        foreach($res as $val) {
            echo '<div class="col-md-4 col-xl-3" style="padding: 15px 15px;">';
                echo '<div class="card">';
                    echo '<div class="card-header">';
                        echo '<h4 class="text-center">'.$val['event_name'].'</h4>';
                    echo '</div>';
                    echo '<div class="card-body">';
                        if($val['event_sub']){
                            echo '<p style="font-family: Montserrat, sans-serif;"  class="text-center">'.$val['event_sub'].'</p>';
                        }else{
                            
                        }
                        
                        echo '<div class="row">';
                        echo '<div class="col text-center"><button class="btn btn-primary border-white" type="button" style="background: rgb(204,31,31);"onclick="location.href=\'event'.'?id='.$val['event_id'].'\'" >More Info</button></div>';
                        echo '</div>';
                    echo '</div>';
                echo '</div>';
            echo '</div>';
            echo '<div class="clearfix"></div>';
        }
        
    }
    ?>
    <header>
        <div class="container"></div>
    </header>
    <main>
        <div class="container">
            <section class="clean-block about-us" style="padding: 0px 0px;">
                <h2 class="text-center text-black-50" style="padding: 25px 10px;/*background-image: linear-gradient(to bottom , #ececec, white);*/font-family: Montserrat, sans-serif;">Welcome to St John's College Lockdown Ents!</h2>
                <p class="text-center" style="width: 90%;margin-left: 5%;max-width: 90%;min-width: 70%;padding: 0px 0px;margin-right: 5%;font-family: Montserrat, sans-serif;">In case you hadn't heard, we're currently in a pandemic. COVID-19 has changed how we live and work in ways unimaginable a year ago. With team sports, music and both John's Bars cancelled for the foreseeable, you may be wondering what to
                    do with yourself. <br><br>Presenting... <strong>SJC Lockdown Ents!</strong><br><br>Over the next 4 weeks, the JCR and SBR Committees will be organising a range of COVID-friendly activities for the college community to get involved
                    in, from puzzles and book clubs to pub quizzes and poker nights. This website will act as a one stop shop for everything going on during Lockdown at St John's. Scroll down to find out more and book a slot!<br></p>
            </section>
        </div>
        <div class="container">
            <section>
                <h2 class="text-center text-black-50" style="padding: 25px 10px;/*background-image: linear-gradient(to bottom , #ececec, white);*/font-family: Montserrat, sans-serif;">What's On?</h2>
            </section>
            <div class="row d-flex" style="border-color: rgb(204,31,31);">
                <?php
                    $dupes = [];
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
                            CreateEvent($val['event_id']);
                        }
                    }
                    
                ?>

            </div>
            <div class="text-center" style="padding-top: 25px;">
            <a class="btn btn-primary btn-lg border-light" data-toggle="collapse" aria-expanded="false" aria-controls="collapse-1" href="#collapse-1" role="button" style="background: rgba(167,167,167,0.15);color: rgb(33,37,41);font-family: Montserrat, sans-serif;">Disclaimer</a>
                <div
                    class="collapse" id="collapse-1" style="padding-top: 25px;">
                    <p style="font-family: Montserrat, sans-serif;">In order to create safe and enjoyable environments for participants in JCR and SBR events, we are asking all attendees to acknowledge the following self-declaration prior to attending in-person events. This and other adaptations are for your
                        benefit and to protect a large number of people from having to self-isolate in the event that someone subsequently becomes symptomatic.&nbsp;<strong>By booking onto an in-person event, you declare the following:</strong><br></p>
                    <ul>
                        <li class="text-left" style="font-family: Montserrat, sans-serif;">In the last 7 days, I have not displayed any of the below symptoms:<br>
                            <ul>
                                <li>Fever<br></li>
                                <li>Continuous cough<br></li>
                                <li>A loss of, or change in, my normal sense of taste or smell<br></li>
                            </ul>
                        </li>
                        <li class="text-left" style="font-family: Montserrat, sans-serif;">To the best of my knowledge, I have not been in contact with anyone infected, suspected or diagnosed, with COVID-19 within the past two weeks. (contact refers to a distance of less than 2 metres, for longer than 15 minutes in the
                            absence of protective face coverings)<br></li>
                        <li class="text-left" style="font-family: Montserrat, sans-serif;">I agree to abide by both College and local guidance in relation to the delivery of the event, such as the use of PPE<br></li>
                        <li class="text-left" style="font-family: Montserrat, sans-serif;">I agree to follow the College guidelines and Public Health England guidelines in relation to hygiene, handwashing, social distancing for the duration of the event (including national guidelines on transport to and from the event)<br></li>
                        <li
                            class="text-left" style="font-family: Montserrat, sans-serif;">I agree that if any of the above changes within 24 hours of the event, I will notify the event organiser to discuss my circumstances.<br></li>
                    </ul>
            </div>
        </div>
        </div>
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