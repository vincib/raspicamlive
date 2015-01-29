

function capturepage() {
    updatecapture();
}


var lastcaptime=0;

function updatecapture() {
    $.ajax({url: "action.php?action=updatecapture",
	  dataType: "json",
	  success: function(data) {
              
              
              if( ! "code" in data || data.code !== 0){
                  $("#notifications").html(data.message);
              }
              var payload=data.payload;
	      if (payload.lastcaptime > lastcaptime || payload.lastcaptime==0) {
		  if ($("#lastcap")) {
		      d = new Date();
		      $("#lastcap").attr("src","lastcap.php?"+d.getTime());
		      lastcaptime=payload.lastcaptime;
		  }
	      }
	      if (payload.isrecording) {
		  if ($("#recordingstatus").attr("src")=="assets/record.png") {
		      $("#recordingstatus").attr("src","assets/norecord.png");
		  } else {
		      $("#recordingstatus").attr("src","assets/record.png");
		  }
	      } else {
		  $("#recordingstatus").attr("src","assets/stop.png");
	      }
	      if (payload.storagesize) {
		  if ($("#storagesize")) {
		      $("#storagesize").html(payload.storagesize);
		      $("#storageused").html(payload.storageused);
		      $("#storageavail").html(payload.storageavail);
		  }
	      }
	      if (payload.currentproject) {
		  if ($("#currentproject")) {
		      $("#currentproject").html(payload.currentproject);
		  }
	      }
	      // redoit in 5 seconds ;) 
	      window.setTimeout(updatecapture,5000);
	  }
	   });
}

function start_recording() {
    $.ajax({url: "action.php?action=startrecording",
	    success: function(data) {
		$("#lastcommand").html(data);
	    }
    });
}

function stop_recording() {
    $.ajax({url: "action.php?action=stoprecording",
	    success: function(data) {
		$("#lastcommand").html(data);
	    }
    });
}


function save_title() {
    $.ajax({url: "action.php?action=savetitle",
	    data: {"rectitle": $("#rectitle").val() },
	    success: function(data) {
		$("#savedtitle").html(data);
		window.setTimeout('$("#savedtitle").html("")',5000);
	    }
    });    
}


function reboot() {
    if (confirm("Are you sure you want to REBOOT the raspilive cam?")) {
	$.ajax({url: "action.php?action=reboot",
		success: function(data) {
		    $("#lastcommand").html(data);
		}
	       });
    }
}

function shutdown() {
    if (confirm("Are you sure you want to SHUTDOWN the raspilive cam?")) {
	$.ajax({url: "action.php?action=shutdown",
		success: function(data) {
		    $("#lastcommand").html(data);
		}
	       });
    }
}
