function swapClasses(mybutton){
   //set the buttons right
   $(".settings-button-selected").addClass("settings-button");
   $(".settings-button-selected").removeClass("settings-button-selected");
   mybutton.addClass("settings-button-selected");
   mybutton.removeClass("settings-button");
   //set the content right
   var string = (mybutton.attr('id')).substring(0,(mybutton.attr('id')).length-5);
   $(".slider").removeClass("hidden");
   $(".slider").addClass("hidden");
   $("#"+string).removeClass("hidden");
}
$(document).ready(function(){
   $(".settings-button").click(function(){
      swapClasses($(this));
   });
   $(".settings-button-selected").click(function(){
      swapClasses($(this));
   });
   $("#anotherUser").click(function(){
      var numOfUsers = $('#newUsers tr').length;
      var string = '<tr><td><input type="text" name="display_name'+numOfUsers+'"></td>';
      string +='<td><input type="text" name="username'+numOfUsers+'"></td>';
      string +='<td><input type="text" name="url_tag'+numOfUsers+'"></td>';
      $("#newUsers > tbody:last").append(string);
   });
   $('#projects').change(function(){
      var projectID = $(this).children("option:selected").val();
      var newUrl = "getTeams.php?project="+projectID;
      console.log(newUrl);
      $.ajax({
         url: newUrl,
         type: 'GET',
         dataType:'json'
         
       }).success(function(d){
            //data should come back as a list for new teams to go in options and a list of the default team members
            console.log(d);
            var inner1 = "";
            $.each(d['team'], function(index){
               inner1 += "<option value='"+d["ID"][index]+"'>"+d['team'][index]+"</option>";
            });
            $('#teams').html(inner1);
            var inner2 = "";
            $.each(d['member'], function(index){
               inner2 += d['member'][index]+"<br>";
            });
            $('#members').html(inner2);
            var inner3 = ""
            $.each(d['nonMemberID'], function(index){
               inner3 += "<option value='"+d["nonMemberID"][index]+"'>"+d['nonMemberName'][index]+"</option>";
            });
            $('#userToAdd').html(inner3);
         });
   });
   $('#teams').change(function(){
      var teamID = $(this).children("option:selected").val();
      var projectID = $('#projects').children("option:selected").val();
      var newUrl = "getTeams.php?project="+projectID+"&team="+teamID;
      $.ajax({
         url: newUrl,
         type: 'GET',
         dataType:'json'
       }).success( function(d){
         //data should just come back as the team members for that project.
         console.log(d);
         var inner2 = "";
          $.each(d["member"], function(index){
            inner2 += d["member"][index]+"<br>";
          });
          $('#members').html(inner2);
          var inner3 = "";
          if(typeof d['nonMemberID'] === 'undefined'){
          
          }else{
             $.each(d['nonMemberID'], function(index){
               inner3 += "<option value='"+d["nonMemberID"][index]+"'>"+d['nonMemberName'][index]+"</option>";
             });
             $('#userToAdd').html(inner3);
          }
      });
   });
   $('#addMember').click(function(){
      var teamID = $(this).children("option:selected").val();
      var projectID = $('#projects').children("option:selected").val();
      var userID = $('#userToAdd').children("option:selected").val();
      var newUrl = "getTeams.php?project="+projectID+"&team="+teamID+"&userToAdd="+userID;
      console.log(newUrl);
      $.ajax({
         url: newUrl,
         type: 'GET',
         dataType:'json'
       }).success( function(d){
          console.log("hi");
          console.log(d);
          //data should just come back as the team members for that project.
          var inner1 = "";
          $.each(d['team'], function(index){
            inner1 += "<option value='"+d["ID"][index]+"'>"+d['team'][index]+"</option>";
          });
          console.debug(inner1);
          $('#teams').html(inner1);
          var inner2 = "";
          $.each(d['member'], function(index){
            inner2 += d['member'][index]+"<br>";
          });
          console.debug(inner2);
          $('#members').html(inner2);
          var inner3 = ""
          $.each(d['nonMemberID'], function(index){
            inner3 += "<option value='"+d["nonMemberID"][index]+"'>"+d['nonMemberName'][index]+"</option>";
          });
          console.debug(inner3);
          $('#userToAdd').html(inner3);
      });
   });
   
});
