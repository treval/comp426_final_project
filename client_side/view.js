var url_base = "https://wwwp.cs.unc.edu/Courses/comp426-f17/users/treval/final_project/server_side";

$(document).ready(function () {

  $.ajax(url_base + "/event.php",
   {type: "GET",
   dataType: "json",
   success: function(event_ids, status, jqXHR) {
       for (var i=0; i<event_ids.length; i++) {
         load_event_item(event_ids[i]);
       }
    }
  });

  var trigger = $('.hamburger'),
  overlay = $('.overlay'),
  isClosed = false;

  trigger.click(function () {
    hamburger_cross();      
  });

  function hamburger_cross() {
    if (isClosed == true) {          
      overlay.hide();
      trigger.removeClass('is-open');
      trigger.addClass('is-closed');
      isClosed = false;
    } else {   
      overlay.show();
      trigger.removeClass('is-closed');
      trigger.addClass('is-open');
      isClosed = true;
    }
  }
  
  $('[data-toggle="offcanvas"]').click(function () {
    $('#wrapper').toggleClass('toggled');
  });  

});

var Event = function(event_json) {
  this.id = event_json.id;
  this.name = event_json.name;
  this.scheduled = event_json.scheduled;
  this.type = event_json.type;
  this.description = event_json.description
};

Event.prototype.makeCollapseEvent = function() {

  var title_div = $("<div data-toggle='collapse' data-target=#"+this.id+"></div>");
  title_div.addClass('title');
  title_div.html(this.name+" "+this.scheduled + " <span class='caret'></span>");

  form = $("<div id="+this.id+" class='form-group collapse'></div>");

  form.append("<input type='text' class='form-control' placeholder='First Name'>");
  form.append("<input type='text' class='form-control' placeholder='Last Name'>");
  form.append("<input type='text' class='form-control' placeholder='E-mail'>");

  form.append("<button type='submit' class='btn btn-default'>Submit</button>");


  $('.event_list').append(title_div);
  $('.event_list').append(form);
};

var load_event_item = function (id) {
  $.ajax(url_base + "/event.php/" + id,
    {type: "GET",
    dataType: "json",
    success: function(event_json, status, jqXHR) {
      var e = new Event(event_json);
      $('.event_list').append(e.makeCollapseEvent());
    }
  });
}