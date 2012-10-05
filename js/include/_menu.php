</head>
<body>
<div id="wrapper">
	<div id="logo">
	    <h1><a href="./index.php">Mobile Sensing System</a></h1>
	</div>
    <div id="navigation">
        <ul id="menu">
	        <li<?php 
	        
	            if(isset($_SERVER["SCRIPT_NAME"])){
	            
	                $SCRIPT = strtolower(basename($_SERVER["SCRIPT_NAME"]));
	            
	            if(strpos($SCRIPT, "index") !== false){
	                echo " class=\"current_page_item\"";
	            }
	        } 
	        
	        ?>><span><a href="./index.php">Home</a></span></li>
	        <li<?php 
	            if(strpos($SCRIPT, "download") !== false){
	                echo " class=\"current_page_item\"";
	            } ?>><span><a href="./download.php">Download</a></span></li>
	        
	        <li<?php 
	            if(strpos($SCRIPT, "about") !== false){
	                echo " class=\"current_page_item\"";
	            } ?>><span><a href="./about.php">About</a></span></li>
	            <?php
	            if(isset($_SESSION["USER_LOGGED_IN"])){
	                ?>
	                <li<?php
	                    if(strpos($SCRIPT, "ucp") !== false){
	                        echo " class=\"current_page_item\"";
	                    } ?>><span><a href="./ucp.php">Control panel</a></span></li>
	                
	            <?php
	            }
	            ?>
		</ul>
	</div>
<!-- end #menu -->