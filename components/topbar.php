<!DOCTYPE html>

<html>
<head>
<link rel="stylesheet" href="components/topbar_style.css">
</head>
<body>

<div class='navbar'>
  <ul class='tb'>
  <li class='tb'><a class='tb' ><b>SJC Ents</b></a></li>
    <div class="dropdown" style="float:right">
    <button class="dropbtn"><?php if($firstname){echo $firstname.' ('.$_SERVER['REMOTE_USER'].')';}else{echo $_SERVER['REMOTE_USER'];}?>
        <i class="fa fa-caret-down"></i>
      </button>
      <div class="dropdown-content">
        <a class='tb' href="/portal/logout">Log Out</a>
      </div>
      </div>
  </ul>
</div>
<br>
</body>
</html>