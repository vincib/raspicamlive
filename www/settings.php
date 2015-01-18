<?php
require_once("header.php");
require_once("common.php");


$fields=array("widthheight" => "Video width and height",
	      "fps" => "Number of images per second",
	      "audiosource" => "Audio peripheral (may be empty for no audio track)",
	      "videobitrate" => "Video bitrate (in kbps)",
	      "audiobitrate" => "Audio bitrate (in kpbs)",
	      );

if (count($_POST)>1) {
  $settings=array();
  foreach($fields as $k=>$v) $settings[$k]=$_POST[$k];
  list($settings["width"],$settings["height"])=explode("x",$settings["widthheight"]);
  unset($settings["widthheight"]);
  setSettings($settings);
}

$settings=getSettings();

$settings["widthheight"]=$settings["width"]."x".$settings["height"];

$awidthheight=array(
		    "640x480",
		    "800x600",
		    "1024x768",
		    "640x360",
		    "1280x720",
		    "1920x1080",
		    );
$afps=array("10","12","20","25","30", "50");

$aaudiosource=array("","hw:1");

$avideobitrate=array("400","500","800","1000","2000","4000","8000","9000","10000","15000","20000");
$aaudiobitrate=array("64","96","128","192","256");
?>
            <div class="row">
              <div class="col-md-6" role="main" id="settingsdiv">


        <h1>Video Settings</h1>

  <form method="post" action="settings.php">
  <?php foreach($fields as $k=>$v) {
	    ?>
<p>
	    <label for="<?php echo $k; ?>"><select name="<?php echo $k; ?>"><?php
	       $ak="a".$k;
	       foreach($$ak as $l) {
	       echo "<option ";
	       if ($settings[$k]==$l) echo " selected=\"selected\"";
	       echo ">".$l."</option>";
	     }
?></select> &nbsp; <?php echo $v; ?> 
</p>
<?php } ?>
<input type="submit" name="go" value="Save the settings" />
</form>

              </div><!-- col6 -->
  <div class="col-md-2">
  </div>
  <div class="col-md-4">

<p>
  <a href="#" onclick="reboot()" ><img src="assets/reboot.png" alt="Reboot" title="Reboot" /></a>
  &nbsp; &nbsp;
  <a href="#" onclick="shutdown()" ><img src="assets/shutdown.png" alt="Shutdown" title="Shutdown" /></a>
</p>
<p><span id="lastcommand"></span></p>

  </div>
</div>

<?php
require_once("footer.php"); 
?>
