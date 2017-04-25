// Holds all common scripts
var sessionID = -1;
// Runs on load
$(document).ready(function() {

});


function validateLogin() {
    var user = document.forms["loginForm"]["userName"].value;
    var pass = document.forms["loginForm"]["passWord"].value;

    if (user == "") {
        $("#userError").html("Username is required!");
        return false;
    }
    if (pass == "") {
        $("#passError").html("Password is required!");
        return false;
    }

}

// Generates a session ID when clicked
function generateSessionID() {
     $.ajax({
        type: "GET",
        url: "../databasehelper.php",
        data: {"function":"generateSessionID"},
        dataType:"json",
        error: function(req, err){ console.log('ERROR ' + err)},
        success: function (data) {
        }
    });
}

// Ends the session
function endSession() {
     $.ajax({
        type: "GET",
        url: "../databasehelper.php",
        data: {"function":"deleteSessionID"},
        dataType:"json",
        error: function(req, err){ console.log('ERROR ' + err)},
        success: function (data) {
          console.log("Successfully deleted session ID");
        }
    });
}

// Gets session ID
function getSessionID() {
     $.ajax({
        type: "GET",
        url: "../databasehelper.php",
        data: {"function":"getSessionID"},
        dataType:"json",
        error: function(req, err){ console.log('ERROR ' + err)},
        success: function (data) {
          if (data) {
            //console.log(data[0]);
            sessionID = data[0].sessionID;
            $("#sessionID").text(sessionID);
            //document.getElementById("sessionID").innerHTML = sessionID;
          }
        }
    });
}

// Hacky header script
function printHeaderProf() {
    var header = "";
    header += "<div class=\"jumbotron professor\" id=\"jumbo\">";
    header += "<a class=\"btn btn-primary logout\" style=\"position: absolute; right: 0;\" href=\"../index.php\">LOGOUT</a>"
    header += "<a href=\"home.html\"><img alt=\"logo\" src=\"../logo-2.0.png\" height=\"100\"></a>";
    header += "<a class=\"nav-link-prof\" href=\"question_sets.html\" id=\"qSets\" " +
        "style=\"margin-left: 40px;\">QUESTION SETS</a>";
    header += "<a class=\"nav-link-prof\" href=\"qlist.html\" id=\"viewQs\">VIEW QUESTIONS</a>";
    header += "<a class=\"nav-link-prof\" href=\"grades.html\" id=\"viewGrades\">VIEW GRADES</a>";
    header += "<a class=\"nav-link-prof\" href=\"roster.html\" id=\"viewRoster\">VIEW ROSTER</a>";
    header += "</div>";
    document.write(header);
}

// Hacky header script
function printHeaderStudent() {
    var header = "";
    header += "<div class=\"jumbotron student\" id=\"jumbo\">";
    header += "<a class=\"btn btn-primary logout-student\" style=\"position: absolute; right: 0;\" href=\"../index.php\">LOGOUT</a>"
    header += "<a href=\"home.html\"><img alt=\"logo\" src=\"../logo-2.0.png\" height=\"100\"></a>";
    header += "<a href=\"joinSession.html\" class=\"nav-link-student\" " +
        "id=\"joinSession\" style=\"margin-left: 40px;\">JOIN A SESSION</a>";
    header += "<a href=\"review.html\" class=\"nav-link-student\" id=\"review\">REVIEW</a>";
    header += "</div>";
    document.write(header);
}

// Hacky header script
function printHeaderLanding() {
    var header = "";
    header += "<div class=\"jumbotron landing\" id=\"jumbo\">";
    header += "<a href=\"index.php\"><img alt=\"logo\" src=\"logo-2.0.png\" height=\"100\"></a>";
    header += "</div>";
    document.write(header);

}

//-------timer functionality----------------

// Calc time based on seconds passed in
function calculateTime(seconds) {
    return new Date(new Date().valueOf() + (seconds * 1000));
}

// Actually does all the timer logic
function startTimer(time) {
    var $clock = $('#timer');
    // Variable to store time left. Used later in resume
    var $leftTime = 0;

    $clock.countdown(calculateTime(time))
    // Updates clock!
    .on('update.countdown', function(event) {
        var $this = $(this);
        $this.html(event.strftime('<span>%M min %S sec</span>'));
    })
    // Handles when timer is passed
    // Stores how many seconds are left
    .on('stoped.countdown', function(event) {
        $leftTime = parseInt(event.offset.totalSeconds)
    })
    // Handles setting things to 0 when timer stops
    // Can add alert pop up/ sounds later
    .on('finish.countdown', function(event) {
        var $this = $(this);
        $this.html(event.strftime('<span>00 min 00 sec</span>'));
    });

    // Handles Reset button
    $('#btn-reset').click(function() {
        // Remakes original timer
        $clock.countdown(calculateTime(time));
    });

    // Handles pause button
    $('#btn-pause').click(function() {
        // Pure looks, highlights button selected
        $(this).addClass('active focus').siblings().removeClass('active focus');
        $clock.countdown('stop')
    });

    // Handles the resume button
    $('#btn-resume').click(function() {
        // Pure looks, highlights button selected
        $(this).addClass('active focus').siblings().removeClass('active focus');
        // Makes new time goal based on $leftTime
        // The 1000 is the offset needed for this
        // particular timer
        var $delta = new Date(new Date().valueOf() + ($leftTime * 1000));
        // Remakes timer with old time left
        $clock.countdown($delta);
    });
}

//--------graph functionality-----------------

existingGraphs = {};
function drawResponseCharts(canvasId,qid) {
  var indexCorrect;
  var labels = [];
  var colors = [];
  var values = [];
  var confInput = [0,0,0,0,0];
  $.ajax({
      type: "GET",
      url: "../databasehelper.php",
      data: {"function":"getQResponse", 'id':qid},
      dataType:"json",
      error: function(req, err){console.log('ERROR ' + err)},
      success: function (data) {
          if(data){
            var len = data.length;
            for (var i = 0; i < len; i++){
              labels.push(data[i]['choice']);
              values.push(0);
              if (data[i]['is_correct'] === "1") {
                colors.push('#C3B708');
              } else {
                colors.push('#64c8ff');
              }
            }
          }
      }
  });
  $.ajax({
      type: "GET",
      url: "../studentdatahelper.php",
      data: {"function":"getStudentResponses"},
      dataType:"json",
      error: function(req, err){console.log('ERROR ' + err)},
      success: function (data) {
          if(data){
            var len = data.length;
            for (var i = 0; i < len; i++){
              for (var n = 0; n < labels.length; n++) {
                if (data[i]['stud_ans'] === labels[n]) {
                  values[n] += 1;
                  break;
                }
              }
              var confidence = parseInt(data[i]['conf_level']);
              confInput[confidence-1] += 1;
            }
          }
      }
  });

  var barData = {
      type: 'bar',
      data: {
        labels : labels,
        datasets : [{
                data : values,
                backgroundColor : colors,
                borderColor : '#000000',
                borderWidth : 1
        }]
      },
      options: getBarOptions(),
  };
  // get bar chart canvas
  var drawSpace = document.getElementById(canvasId).getContext("2d");
  // draw bar chart
  if (existingGraphs[canvasId] != undefined) {
    existingGraphs[canvasId].destroy();
  }
  existingGraphs[canvasId] = new Chart(drawSpace, barData, {
    responsive: true,
    maintainAspectRatio: false
  });
  return confInput;
};

function drawBarGraph(canvasId, labels, data, indexCorrect) {
  var barColors = getBarColors(labels.length, indexCorrect);
  var barData = {
      type: 'bar',
      data: {
        labels : labels,
        datasets : [{
                data : data,
                backgroundColor : barColors,
                borderColor : '#000000',
                borderWidth : 1
        }]
      },
      options: getBarOptions(),
  };
  // get bar chart canvas
  var drawSpace = document.getElementById(canvasId).getContext("2d");
  // draw bar chart
  if (existingGraphs[canvasId] != undefined) {
    existingGraphs[canvasId].destroy();
  }
  existingGraphs[canvasId] = new Chart(drawSpace, barData, {
    responsive: true,
    maintainAspectRatio: false
  });
}

function getBarOptions() {
  var options = {
    legend: {
      display: false
    },
    tooltips: {
      enabled: false
    },
    hover: {
      animationDuration: 0
    },
    scales: {
      yAxes: [{
        ticks: {
          beginAtZero:true
        }
      }]
    },
    animation: {
      onComplete: function() {
        this.chart.controller.draw();
        drawValue(this, 1);
      },
      onProgress: function(state) {
        var animation = state.animationObject;
        drawValue(this, animation.currentStep / animation.numSteps);
      }
    }
  }
  var insideFontColor = '255,255,255';
  var outsideFontColor = '0,0,0';
  // How close to the top edge bar can be before the value is put inside it
  var topThreshold = 20;
  var modifyCtx = function(ctx) {
    ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, 'normal', Chart.defaults.global.defaultFontFamily);
    ctx.textAlign = 'center';
    ctx.textBaseline = 'bottom';
    return ctx;
  };
  var fadeIn = function(ctx, obj, x, y, black, step) {
    var ctx = modifyCtx(ctx);
    var alpha = 0;
    ctx.fillStyle = black ? 'rgba(' + outsideFontColor + ',' + step + ')' : 'rgba(' + insideFontColor + ',' + step + ')';
    ctx.fillText(obj, x, y);
  };
  var drawValue = function(context, step) {
    var ctx = context.chart.ctx;
    context.data.datasets.forEach(function (dataset) {
      for (var i = 0; i < dataset.data.length; i++) {
        if (dataset._meta[Object.keys(dataset._meta)[0]].data[i]){
          var model = dataset._meta[Object.keys(dataset._meta)[0]].data[i]._model;
          var textY = (model.y > topThreshold) ? model.y - 3 : model.y + 20;
          fadeIn(ctx, dataset.data[i], model.x, textY, model.y > topThreshold, step);
        }
      }
    });
  };
  return options;
}

function getBarColors(numAnswers, indexCorrect) {
  var colors = [];
  var n = 0;
  while (n < numAnswers) {
    colors[n] = '#64c8ff';
    n++;
  }
  if (indexCorrect != 0) {
      colors[indexCorrect-1] = '#C3B708';
  }
  return colors;
}
