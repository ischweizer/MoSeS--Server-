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
                <a href="#">Devices</a>
            </li>
             <li class="dropdown">
                <a href="#">Groups <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li>
                        <a href="#">View</a>
                    </li>
                    <li>
                        <a href="#">Create</a>
                    </li>
                </ul>
            </li>
             <li class="dropdown">
                <a href="#">Studies <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li>
                        <a href="#">View</a>
                    </li>
                    <li>
                        <a href="#">Create</a>
                    </li>
                </ul>
            </li><?php
                     }
                 ?>
             <li class="dropdown">
                <a href="#">About <b class="caret"></b></a>
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
            </li>
          </ul>
        <!--</div>-->
    </div><!-- /.navbar-inner -->
    </div><!-- /.navbar -->