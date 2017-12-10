//Make sure to change this to your folder (not treval), and upload all of the server side files with scp.
var url_base = "https://wwwp.cs.unc.edu/Courses/comp426-f17/users/treval/final_project/server_side";

$(document).ready(function () {

  /*
  This is where you'll put all the client side functionality. You'll need more than the ajax requests I put here, but these are samples.
  So just follow the examples to get data from the server. 

  Let me know if: 
    1. You have questions about the orms or RESTful interface
      (Should be mainly RESTful interface, the ORM's are pretty much under the covers)
    2. You ever get any 500 error in your web inspector. Those are probably on me, and I'll try to fix them or tell you what's going on
       ASAP.
  */

  //Displays all events currently in database.
  renderEvents();

  //This appends all events in the database to the event div. It's called on document.ready to have them there to start,
  //but it's also called whenever a new event is submitted.
  function renderEvents() {
    $.ajax(url_base + "/event.php",
     {type: "GET",
     dataType: "json",
     success: function(event_ids, status, jqXHR) {
          $('.event_list').empty();
           for (var i=0; i<event_ids.length; i++) {
             load_event_item(event_ids[i]);
           }
      }
    });
  }

  //These next two blocks submit a user or new event, respectively. Each are very similar. They are ajax POSTS using their 
  //respective php file. They serialize the data from the form. On success, they create a usable js object bases on the actual
  //model data. The difference is that a new event submission also runs renderEvents().
  $('#new_user_form').on('submit',
    function (e) {
      e.preventDefault();
      $.ajax(url_base + "/user.php",
        {type: "POST",
        dataType: "json",
        data: $(this).serialize(),
        success: function(user_json, status, jqXHR) {
          u = new User(user_json);
        },
        error: function(jqXHR, status, error) {
          alert(jqXHR.responseText);
        }});
  });

  $('#new_event_form').on('submit',
    function (e) {
      e.preventDefault();
      $.ajax(url_base + "/event.php",
        {type: "POST",
        dataType: "json",
        data: $(this).serialize(),
        success: function(event_json, status, jqXHR) {
          e = new Event(event_json);
          renderEvents()
        },
        error: function(jqXHR, status, error) {
          alert(jqXHR.responseText);
        }});
  });

  //Just for menu and styling stuff.
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


// Constructors for each event. Run on successful ajax requests.
var Event = function(event_json) {
  this.id = event_json.id;
  this.name = event_json.name;
  this.scheduled = event_json.scheduled;
  this.type = event_json.type;
  this.description = event_json.description
};

var User = function(user_json) {
  this.id = user_json.id;
  this.first = user_json.first;
  this.last = user_json.last;
  this.email = user_json.email;
};

var Rsvp = function(rsvp_json) {
  this.id = rsvp_json.id;
  this.uid = rsvp_json.uid;
  this.eid = rsvp_json.eid;
}

//Makes the div that contains event info. Might need modification for display, style, etc. You can write
//something similar if you need to display users, rsvps, etc. 
Event.prototype.makeCollapseEvent = function() {

  var event_div = $("<div class='event_div'></div>");

  var title_div = $("<div></div>");
  title_div.addClass('title');
  title_div.html(this.name+" "+this.scheduled + " <span class='caret'></span>");

  event_div.append(title_div);
  event_div.append("<div id='new_user_div'><form id='new_user_form'><input name='first' type=text><br><input name='last' type=text><br><input name='email' type=text><br><button type=submit>Create</button></form></div>");

  event_div.data('event', this);
  return event_div;

};

//This loads a single item by id (/event.php/ + id) with a GET. On success, it creates an Event object e and appends the event
//representation using makeCollapseEvent(); This is called within renderEvents().
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