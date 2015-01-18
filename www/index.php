<?php
require_once("header.php");
require_once("common.php");

$metadata=getProjectMetadata();
?>
            <div class="row">
              <div class="col-md-6" role="main" id="capturecontrol">


        <h1>Capture</h1>

<p>Recording status 
   &nbsp; &nbsp; &nbsp; 
   <img id="recordingstatus" alt="" src="assets/help.png" />
</p>
<p>Recording buttons 
   &nbsp; &nbsp; &nbsp; 
   <a href="#" onclick="start_recording()"><img alt="Record" src="assets/record.png"/></a>
   &nbsp; &nbsp; &nbsp; 
   <a href="#" onclick="stop_recording()"><img alt="Stop" src="assets/stop.png"/></a>
		   &nbsp; &nbsp; &nbsp;
   <span id="lastcommand">  -- -- </span>
</p>

<p>Storage size <b><span id="storagesize">unknown</span></b> MB used <b><span id="storageused">unknown</span></b> MB available <b><span id="storageavail">unknown</span></b> MB</p>
<p>Current recording folder  <b><span id="currentproject">unknown</span></b></p>

<p>Title of the current project 
<br /><input type="text" id="rectitle" name="rectitle" style="width: 400px" value="<?php echo $metadata["title"]; ?>" /><button onclick="save_title()">Save</button>
<br /><span id="savedtitle"></span>
</p>

              </div><!-- col6 -->
              <div class="col-md-6" role="main" id="capturecontrol">

<!-- image fallback -->
<!-- 		<img id="lastcap" src="lastcap.php" alt="Last Captured picture" style="max-width: 100%"/>
  -->
  <object id="preview" width="640" height="360" type="application/x-shockwave-flash" data="flashmediaelement.swf">
        <param name="movie" value="flashmediaelement.swf" />
        <param name="flashvars" value="autoplay=true&controls=true&file=live.php" />
    </object>  
<script type="text/javascript">
				   //  $(function () { preview.play();  });
</script>
              </div><!-- col6 -->
            </div><!-- row -->


	    <script type="application/javascript">
	      $(function () {
	      // on capture page, when ready, update lastcap & recstatus every 5 seconds
	      capturepage();
	      });
	    </script>

<?php
require_once("footer.php"); 
?>
