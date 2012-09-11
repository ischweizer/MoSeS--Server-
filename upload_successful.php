<?php
	//Selecting parameters from APK table
	$sql = "SELECT * FROM ". $CONFIG['DB_TABLE']['APK'] ." WHERE apkid in (select max(apkid) FROM ". $CONFIG['DB_TABLE']['APK']." WHERE userid = ".$_SESSION["USER_ID"].");";
    $res=$db->query($sql);
    $row = $res->fetch();
    if(!empty($row))
    { 
	    $apktitle = $row['apktitle'];
	    $android_version = $row['androidversion'];
	    $startdate = $row['startdate'];
	    $startcriterion = $row['startcriterion']; 
	    $enddate = $row['enddate'];
	    $runningtime = $row['runningtime'];
	    $description = $row['description'];
	    $sensors = $row['sensors'];
	    $onlymygroup = $row['locked'];
	    $invite = $row['inviteinstall'];
	    $maxdevice = $row['maxdevice'];
	    $apkname = $row['apkname'];
	}
?>
<!-- Feedback about successfully upload of the apk-->
<b>The name of this user study (title): </b><?php echo $apktitle; ?>
<br>
<br>
<b>The lowest android version you choosed to run this user study: </b><?php echo $android_version; ?>
<br> 
<br>
<b><?php echo $apktitle; ?> will commince
<?php
	if($startdate != null && strtotime($startdate) > strtotime(date("Y-m-d", mktime(0, 0, 0, 0, 0, 0000))))
		echo " on: </b>".$startdate;
	elseif($startcriterion != "0")
			echo " after </b>".$startcriterion." <b>users join ".$apktitle."</b>";
	else
		echo " immediately";
?>
<br>
<b><?php echo $apktitle; ?> will be terminated
<?php
	if($enddate != null && strtotime($enddate) > strtotime(date("Y-m-d", mktime(0, 0, 0, 0, 0, 0000))))
		echo " on: </b>".$enddate;
	elseif($runningtime != null)
		echo " </b>".$runningtime." <b> after the start date.</b>";
	else
		echo " immediately";

?>
<br>
<br>
<b>The description of <?php echo $apktitle; ?>:</b>
<?php
	echo $description;
?>
<br>
<br>
<?php
	$sensors_info = array(
				array('accelerometer', 'accelerometer_pressed', 'Accelerometer sensor'),
                array('magnetic_field', 'magnetic_field_pressed', 'Magnetic field sensor'),
                array('orientation', 'orientation_pressed', 'Orientation sensor'),
                array('gyroscope', 'gyroscope_pressed', 'Gyroscope sensor'),
                array('light', 'light_pressed', 'Light sensor'),
                array('pressure', 'pressure_pressed', 'Pressure sensor'),
                array('temp', 'temperature_pressed', 'Temperature sensor'),
                array('proximity', 'proximity_pressed', 'Proximity sensor'),
                array('gravity', 'gravity_pressed', 'Gravity sensor'),
                array('linear_acceleration', 'linear_acceleration_pressed', 'Linear acceleration sensor'),
                array('rotation', 'rotation_pressed', 'Rotation sensor'),
                array('humidity', 'humidity_pressed', 'Humidity sensor'),
                array('ambient_temp', 'ambient_temperature_pressed', 'Ambient temperature sensor'));                
    $apk_to_update_sensors = json_decode($sensors);
    
	if(count($apk_to_update_sensors) > 0)
    {
    	echo '<b>The sensors that '.$apktitle.' requires are: </b>';
	    for($i=0; $i < count($sensors_info); $i++)
	    {
	        if(in_array(($i+1), $apk_to_update_sensors))
	        {
	            // if sensor was selected, check and select it here
	            echo '<img src="images/sensors/ultrasmall/'.$sensors_info[$i][0].'_sensor.png" alt="'.$sensors_info[$i][2].'" title="'.$sensors_info[$i][2].'" />';
	        }
        }
    }
    else
    	echo '<b>You decided that '.$apktitle.' does not require any sensor.</b>';
?>
<br>
<br> 
<?php
	echo "<b>You decided that ".$apktitle." ";
	if($invite == 0)
		echo "will be </b>private for your group.";
	elseif($invite == 1)
		echo "can be joined </b>only from users that you invited.";
	elseif($invite == 2)
		echo "can be joined </b>from all users that were invited and installed ".$apktitle.".";
	elseif($invite == 3)
		echo "can be joined </b>from all users that installed it.";
?>
<br>
<br>
<b>Max number of Devices: </b><?php echo $maxdevice; ?>
<br>
<br>
<b>Your file was titled with: </b><?php echo $apkname; ?> 







