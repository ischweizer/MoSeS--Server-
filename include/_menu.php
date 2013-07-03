</head>
<body>

<div class="wrap">

<div class="container">

    <?php
        if(isset($_SESSION["USER_LOGGED_IN"]) && $_SESSION["USER_LOGGED_IN"] == 1){
    ?>
    <a href="logout.php" class="btn btn-danger" id="btn_logout"><i class="icon-user"></i>Logout</a>        
    <?php
        }else{   
    ?>
    <a class="btn" id="btn_login"><i class="icon-user"></i>Login</a>
    <?php
        }
    ?>

    <div class="masthead">
    <h1 class="muted text-center tmp tmp2" style="font-size: 50pt; margin-top: 20pt; margin-bottom: 30pt;">MoSeS</h1>
    <h5 class="muted text-center tmp tmp_special">Mobile Sensing System</h5>
    
    <div class="navbar">
      <div class="navbar-inner">
          <ul class="nav">
            <li class="dropdown">
                <a href="<?php echo dirname($_SERVER['PHP_SELF'])."/"; ?>">Home</a>
            </li><?php
            
                if(isset($_SESSION["USER_LOGGED_IN"]) && $_SESSION["USER_LOGGED_IN"] == 1){
            
            ?>
            <li class="dropdown">
                <a href="devices.php">Devices</a>
            </li>
             <li class="dropdown">
                <a href="group.php">Groups <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li>
                        <a href="group.php">View</a>
                    </li>
                    <li>
                        <a href="group.php?m=new">Join/Create</a>
                    </li>
                </ul>
            </li>
             <li class="dropdown">
                <a href="study.php">Studies <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li>
                        <a href="study.php">View</a>
                    </li>
                    <li>
                        <a href="#">Create</a>
                    </li>
                </ul>
            </li><?php
                     }
                 ?>
             <li class="dropdown">
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
                     
                 <li class="dropdown">
                    <a href="admin.php">Admin</a>
                 </li>
                 <?php
                 }
                 ?>
          </ul>
        <!--</div>-->
    </div><!-- /.navbar-inner -->
    </div><!-- /.navbar -->