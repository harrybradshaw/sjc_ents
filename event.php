<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>sjcents</title>
    <link rel="stylesheet" href="templates/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat">
    <link rel="stylesheet" href="templates/assets/fonts/ionicons.min.css">
    <link rel="stylesheet" href="templates/assets/css/Footer-Dark.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.2.0/aos.css">
    <link rel="stylesheet" href="templates/assets/css/styles.css">
</head>

<body>
    <?php
    include('db_connect.php');
    if($_GET['id']){
        $sql = "SELECT * FROM hlb_events WHERE NOW() < event_end_datetime AND event_id = ".$_GET['id']." ORDER BY event_start_datetime"; 
        $res = $con_sbr->query($sql);
    }else{

    }
   
    ?>
    <main>
        <div class="container">
            <div class="row" data-aos="fade-up" data-aos-duration="700" style="padding-top: 20px;padding-bottom: 20px;">
                <div class="col text-center">
                    <?php
                        if($_GET['id']){
                            foreach($res as $val) {
                                echo  '<h1 class="text-left">'.$val['event_name'].'<br></h1>';
                                echo '<h3 class="text-left text-secondary">'.$val['event_sub'].'</h3>';
                            }
                        }else{
                            ?>
                            <h1 class="text-left">Oops!<br></h1>
                            <h3 class="text-left text-secondary">This doesn't exist.</h3>
                            <?php
                        }
                    ?>
                    
                    
                </div>
            </div>
            <div class="row" data-aos="fade-up" data-aos-duration="700">
                <div class="col-xl-5"><img class="img-fluid border rounded" src="Academic.jpg"></div>
                <div class="clearfix"></div>
                <div class="col text-center">
                    <div style="padding-top: 10px;">
                    <?php
                    if($_GET['id']){
                            foreach($res as $val) {
                                if($val['event_desc']){
                                    echo '<p style="padding: 5px;">'.$val['event_desc'].'<br></p>';
                                }else{
                                    ?>
                                        <p style="padding: 5px;">More information to follow!<br></p>
                                    <?php
                                }
                                echo '';
                            }
                        }
                    ?>
                        
                        <div class="row">
                            <div class="col-xl-12" style="padding-top: 10px;"><button class="btn btn-primary" type="button">Facebook Event</button></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="templates/assets/js/jquery.min.js"></script>
    <script src="templates/assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="templates/assets/js/bs-init.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.2.0/aos.js"></script>
</body>

</html>