

function capturepage() {
    updatecapture();
}


var lastcaptime=0;

function updatecapture() {
    $.ajax({url: "action.php?action=updatecapture",
	  dataType: "json",
	  success: function(data) {
	      if (data.lastcaptime > lastcaptime || data.lastcaptime==0) {
		  if ($("#lastcap")) {
		      d = new Date();
		      $("#lastcap").attr("src","lastcap.php?"+d.getTime());
		      lastcaptime=data.lastcaptime;
		  }
	      }
	      if (data.isrecording) {
		  if ($("#recordingstatus").attr("src")=="assets/record.png") {
		      $("#recordingstatus").attr("src","assets/norecord.png");
		  } else {
		      $("#recordingstatus").attr("src","assets/record.png");
		  }
	      } else {
		  $("#recordingstatus").attr("src","assets/stop.png");
	      }
	      if (data.storagesize) {
		  if ($("#storagesize")) {
		      $("#storagesize").html(data.storagesize);
		      $("#storageused").html(data.storageused);
		      $("#storageavail").html(data.storageavail);
		  }
	      }
	      if (data.currentproject) {
		  if ($("#currentproject")) {
		      $("#currentproject").html(data.currentproject);
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