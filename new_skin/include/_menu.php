</head>
<body>
<div id="wrapper">
    <div id="menu">
        <ul>
        <li <?php 
        
            if(isset($_SERVER["SCRIPT_NAME"])){
            
                $SCRIPT = strtolower(basename($_SERVER["SCRIPT_NAME"]));
            
            if(strpos($SCRIPT, "index") !== false){
                echo "class=\"current_page_item\"";
            }
        } 
        
        ?>><span><a href="./index.php">Home</a></span></li>
        <li <?php 
            if(strpos($SCRIPT, "download") !== false){
                echo "class=\"current_page_item\"";
            } ?>><span><a href="./download.php">Download</a></span></li>
        <li <?php 
            if(strpos($SCRIPT, "docs") !== false){
                echo "class=\"current_page_item\"";
            } ?>><span><a href="./docs.php">Docs</a></span></li>
        <li <?php 
            if(strpos($SCRIPT, "about") !== false){
                echo "class=\"current_page_item\"";
            } ?>><span><a href="./about.php">About</a></span></li>
</ul>
</div>
<!-- end #menu -->

<?php

if(isset($_SESSION["USER_LOGGED_IN"])){
    
/*
?>
<div class="clear"></div>
<ul class="greet_user">
    <li>Hi, <a href="./ucp.php" title="User control panel"><?php echo $_SESSION["FIRSTNAME"]. " ". $_SESSION["LASTNAME"]. "</a>!"; ?></li>
    <li><a href="./logout.php">LOGOUT</a></li>
</ul>

<?php
*/

}
?>