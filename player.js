var delay = 50;
var fileList = [];
var frame = 0;
var reverse = true;
var stopped = true;
var t = 50;
var pps = 50;
var imgIsBound = false;

var tsEven = 0;
var tsOdd = 0;
var oldLoadTime = 0;

var finishedLoading = false;

var cam;

function displayPic(pic) {
  //console.log(pic.name);
  $("#img").attr('src', pic.name);
  $("#name").text(pic.name);
  $("#timestamp").text(pic.timestamp);
}

function displayDirSize(myCam) {
  $.getJSON( 'main.php?get=get_directory_size&path='+myCam, function( data ) {
    $("#size").text(Math.round(data[0]/1024/1024)+" MB");
    $("#filecount").text(data[1]+" Files");
  })
  .fail(function() {
    console.log( "Error fetching json for dir size." );
  });
}

function lock(isLocked) {
  finishedLoading = !isLocked;
  if (isLocked) {
    $("#img").hide();
    $("#img-loading").show();
  } else {
    $("#img-loading").hide();
  }
}

function switchDir(myCam) {
  lock(true);
  $("#path").text(myCam);
  displayDirSize(myCam);

  var url = 'main.php?get=ls_pictures&path='+myCam;
  // if ( $('#filter').prop('checked') ) {
  //   url += '&t='+ $('#t').val() +'&pps='+ $('#pps').val();
  // }

  $.getJSON( url, function( data ) {
    fileList = (data[0].name == null) ? [] : data ;
    frame = 0;
    updateStatusBar(true);
    //$("#filecount").text(fileList.length+" Files");
  })
  .done(function() {
    lock(false);
    //console.log("done!");
  })
  .fail(function() {
    console.log( "Error fetching json for file list." );
    alert("Error: Failed to create index of images!");
  });
}

function remainingTime() {
  // only calculate time if image load time is known
  if (tsEven == 0 || tsOdd == 0) {
    return "";
  }

  var picsLeft = fileList.length-frame;

  var imgLoadTime = Math.abs(tsEven-tsOdd);

  // only update load time if it was different by quite a bit
  if (oldLoadTime == 0 && Math.abs(imgLoadTime - oldLoadTime) > 50) {
    oldLoadTime = imgLoadTime;
  }

  var time = picsLeft/( 1000/(delay+oldLoadTime) );
  var h = Math.floor(time/60/60);
  var min = Math.floor(time/60%60);
  var sec = Math.floor(time%60);

  var out = "";
  out += (h > 0) ? h+"h " : "";
  out += (min > 0) ? min+"min " : "";

  return "(" + out + sec+"s)";
}

function updateStatusBar(init) {
  if (init) {
    $(".progress-bar").attr("aria-valuemin", (reverse)?fileList.length:0);
    $(".progress-bar").attr("aria-valuemax", (reverse)?0:fileList.length);
    if (reverse) {
      $(".progress-bar").css("width", "100%").attr("aria-valuenow", fileList.length)
    }
  }
  var text;
  if (reverse) {
    text = (fileList.length == 0) ? "0/0" : fileList.length-frame + "/"
                                            + fileList.length + " "
                                            + remainingTime();
    $(".progress-bar").attr("aria-valuenow", frame)
      .css("width", 100-Math.round(frame*100/fileList.length) + "%")
      .text(text);
  } else {
    text = (fileList.length == 0) ? "0/0" : (frame+1) + "/" + fileList.length
                                            + " " + remainingTime();
    $(".progress-bar").attr("aria-valuenow", frame)
      .css("width", Math.round(frame*100/fileList.length) + "%")
      .text(text);
  }
}

// function play() {
//   if (!stopped && frame < fileList.length) {
//     if (reverse) {
//       displayPic(fileList[fileList.length-1-frame]);
//       updateStatusBar();
//     } else {
//       displayPic(fileList[frame]);
//       updateStatusBar();
//     }
//     ++frame;
//     setTimeout(play, delay);
//   }
// }

function play() {
  if (!stopped && frame < fileList.length) {
    var pic = "";
    if (reverse) {
      pic = fileList[fileList.length-1-frame];
    } else {
      pic = fileList[frame];
    }
    displayPic(pic);
    updateStatusBar();
    ++frame;

    // image is only loaded if old one has loaded completly and after delay
    if (!imgIsBound) {
      $("#img").load(function() {
          if (frame%2 == 0) {
            tsEven = Date.now();
          } else {
            tsOdd = Date.now();
          }
          setTimeout(play, delay);
      });
      imgIsBound = true;
    }
  }
}

function togglePlayBtn() {
  if (stopped) {
    stopped = false;
    reverse = $('#reverse').prop('checked');
    if ($('#startframe-checkbox').prop('checked')) {
      frame = (reverse) ? fileList.length - parseInt( $('#startframe').val() ) : parseInt( $('#startframe').val() );
    }
    $("#playbtn").removeClass("btn-primary").addClass("btn-warning");
    $("#playbtn i").removeClass("glyphicon glyphicon-play").addClass("glyphicon glyphicon-pause");
  } else {
    stopped = true;
    $("#playbtn").removeClass("btn-warning").addClass("btn-primary");
    $("#playbtn i").removeClass("glyphicon glyphicon-pause").addClass("glyphicon glyphicon-play");
  }
}

// fast rewind
function frwdBtn() {
  frame = 0;
  if (!stopped) {
    togglePlayBtn();
  }
  reverse = $('#reverse').prop('checked');

  stopped = true;
  updateStatusBar(true);
  play();
}

function reloadBtn() {


  switchDir(cam);
  stopped = true;
  updateStatusBar(true);
}

function updateCameraList() {
  $.getJSON( 'main.php?get=ls_cameras', function( data ) {
    $("#cameralist").empty().append(function() {
        var output = '';
        $.each(data, function(key, value) {
            output += '<option>' + value + '</option>';
        });
        return output;
    });
  })
  .done(function() {
    onCamChange();
  })
  .fail(function() {
    console.log( "Error fetching json of camera list." );
  });
}

function onCamChange() {
  cam = $("#cameralist option:selected").text();
  switchDir(cam);
}

$(function() {
  updateCameraList();

  //$("#pps").attr("value", pps);
  //$("#t").attr("value", t);

  $("#playbtn").click(function() {
    if(finishedLoading && fileList.length != 0) {
      var hasStartframe = $('#startframe-checkbox').prop('checked');
      //console.log("starting at frame "+frame);
      togglePlayBtn();
      $("#img").show();
      play();
    }
  });
  $("#reloadbtn").click(function() {
    if(finishedLoading) {
      reloadBtn();
    } else {
      alert('Loading... Please wait.');
    }
  });

  $("#cameralist").change(function() {
    if(finishedLoading) {
      onCamChange();
    } else {
      alert('Loading... Please wait.');
    }
  });

  $("#deletebtn").click(function() {
    $.getJSON( 'main.php?get=delete&path='+cam, function( data ) {
      $("#msg").html('<br><br><div class="alert alert-success" role="alert">\
        <b>Deletion successful!</b> All files in <em>'+data+'</em> removed. \
        Please refresh page!</div>');
    })
  });
});
