<?php

/*
 * @author: Wladimir Schmidt
 */
           
?></head>
<body>

<div class="wrap">

<div class="container">

    <?php
        if(isset($_SESSION["USER_LOGGED_IN"]) && $_SESSION["USER_LOGGED_IN"] == 1){
    ?>
    <div class="btnLoginProfileCtrl">
        <a href="profile.php" class="btn btn_profile" title="User profile"><i class="icon-pencil"></i>Profile</a>        
        <a href="logout.php" class="btn btn_logout" title="Logout"><i class="icon-off"></i></a> 
    </div>
    <?php
        }else{   
    ?>
    <a class="btn btn_login"><i class="icon-user"></i>Login</a>
    <?php
        }
    ?>

    <div class="masthead">
        <h1 class="muted text-center tmp tmp2" style="font-size: 50pt; margin-top: 20pt; margin-bottom: 30pt;">MoSeS</h1>
        <h5 class="muted text-center tmp tmp_special">Mobile Sensing System</h5>
    </div>
    
    <div class="navbar">
      <div class="navbar-inner">
          <ul class="nav">
            <li class="dropdown nav-menu1">
                <a href="<?php echo dirname($_SERVER['PHP_SELF']); ?>">Home</a>
            </li><?php
            
                if(isset($_SESSION["USER_LOGGED_IN"]) && $_SESSION["USER_LOGGED_IN"] == 1){
            
                    // only confirmed users can see this
                    if(isset($_SESSION['GROUP_ID']) && $_SESSION['GROUP_ID'] > 0){
            ?>
            <li class="dropdown nav-menu2">
                <a href="devices.php">Devices</a>
            </li>
             <li class="dropdown nav-menu3">
                <a href="group.php">Groups <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li>
                        <a href="group.php">View</a>
                    </li>
                    <li>
                        <a href="group.php?m=new">Join/Create</a>
                    </li>
                </ul>
            </li><?php
                  }
                 // only scientist and admins can see this 
                 if(isset($_SESSION['GROUP_ID']) && $_SESSION['GROUP_ID'] > 1){    
                 ?>
             <li class="dropdown nav-menu4">
                <a href="study.php">Studies <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li>
                        <a href="study.php">View</a>
                    </li>
                    <li>
                        <a href="study.php?m=new">Create</a>
                    </li>
                </ul>
            </li><?php
                 }
            }
                 ?>
             <li class="dropdown nav-menu6">
                <a href="about.php">About <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li>
                        <a href="about.php">Project</a>
                    </li>
                    <li>
                        <a href="developers.php">Developers</a>
                    </li>
                    <li>
                        <a href="impressum.php">Impressum</a>
                    </li>
                </ul>
            </li><?php
                 
                 if(isset($_SESSION["ADMIN_ACCOUNT"]) && $_SESSION["ADMIN_ACCOUNT"] == "YES"){
                 ?>
                     
                 <li class="dropdown nav-menu7">
                    <a href="admin.php">Admin</a>
                 </li>
                 <?php
                 }
                 ?>
          </ul>
        <!--</div>-->
    </div><!-- /.navbar-inner -->
    </div><!-- /.navbar -->