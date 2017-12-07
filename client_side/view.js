var url_base = "https://wwwp.cs.unc.edu/Courses/comp426-f17/users/treval/final_test/server_side";

$(document).ready(function () {

  $.ajax(url_base + "/user.php",
   {type: "GET",
   dataType: "json",
   success: function(user_ids, status, jqXHR) {
       for (var i=0; i<user_ids.length; i++) {
         load_user_item(user_ids[i]);
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

var User = function(user_json) {
    this.id = user_json.id;
    this.first = user_json.first;
    this.last = user_json.last;
    this.email = user_json.email;
};

User.prototype.makeUserDiv = function() {
    var div = $("<div></div>");
    div.addClass('user');

    div.data('user', this);

    return div;
};

var load_user_item = function (id) {
  $.ajax(url_base + "/user.php/" + id,
    {type: "GET",
    dataType: "json",
    success: function(user_json, status, jqXHR) {
      console.log(user_json);
      var u = new User(user_json);
      $('.event_list').append("<p>"+u.first + " " + u.last + " " + u.email+"<p>");
    }
  });
}