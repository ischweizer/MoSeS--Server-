</head>
<body>
<div class="wrapper">
<div class="LOGO_TOP"></div>
<div class="PROJECT_NAME"><h1>MoSeS</h1></div>

<div class="clear"></div>

<ul class="menubar">
    <li<?php if(isset($_SERVER["SCRIPT_NAME"])){
        
        $SCRIPT = strtolower(basename($_SERVER["SCRIPT_NAME"]));
        
        if(strpos($SCRIPT, "index") !== false){
            echo " id=\"menu_selected\"";
        }
          
    } ?>><a href="./">HOME</a></li>
    <li<?php 
        if(strpos($SCRIPT, "download") !== false){
            echo " id=\"menu_selected\"";
        } ?>><a href="./download.php">DOWNLOAD</a></li>
    <li<?php 
        if(strpos($SCRIPT, "docs") !== false){
            echo " id=\"menu_selected\"";
        } ?>><a href="./docs.php">DOCU</a></li>
    <li<?php 
        if(strpos($SCRIPT, "about") !== false){
            echo " id=\"menu_selected\"";
        } ?>><a href="./about.php">ABOUT</a></li>
</ul>

<?php

if(isset($_SESSION["USER_LOGGED_IN"])){

?>
<div class="clear"></div>
<ul class="greet_user">
    <li>Hi, <a href="./ucp.php" title="User control panel"><?php echo $_SESSION["FIRSTNAME"]. " ". $_SESSION["LASTNAME"]. "</a>!"; ?></li>
    <li><a href="./logout.php">LOGOUT</a></li>
</ul>

<?php
}
?>

<div class="clear"></div>