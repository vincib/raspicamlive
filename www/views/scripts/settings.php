<div class="row">
    <div class="col-md-6" role="main" id="settingsdiv">
        <h1>Video Settings</h1>
        <br>
        <form method="post" action="/?action=settings" class="form-horizontal">
            <?php foreach ($fields as $k => $v) : ?>
                <div class="form-group">
                    <label for="<?php echo $k; ?>" class="col-sm-8 control-label" ><?= $v ?></label>
                    <div class="col-sm-4">
                        <select class="form-control" name="<?php echo $k; ?>"><?php
                            $ak = "a" . $k;
                            foreach ($$ak as $l) {
                                echo "<option ";
                                if ($settings[$k] == $l)
                                    echo " selected=\"selected\"";
                                echo ">" . $l . "</option>";
                            }
                            ?></select> &nbsp; 
                    </div>
                </div>
            <?php endforeach; ?>
                <div class="form-group">
                    <label  class="col-sm-8 control-label" ></label>
                    <div class="col-sm-4">
                        <input type="submit" class="form-control btn btn-success" name="go" value="Save the settings" />
                    </div>
                </div>
            
        </form>
    </div><!-- col6 -->
    <div class="col-md-4 col-md-offset-2">
        <p>
            <a href="#" onclick="reboot()" ><img src="/assets/reboot.png" alt="Reboot" title="Reboot" /></a>
            &nbsp; &nbsp;
            <a href="#" onclick="shutdown()" ><img src="/assets/shutdown.png" alt="Shutdown" title="Shutdown" /></a>
        </p>
        <p><span id="lastcommand"></span></p>
    </div>
</div>
