function fillTable(catalog){
   $.each(catalog.courses,function(id,name,description,credits){
      $('#catalog-table').append($('<tr class="catalog-row" id ="'+name.name+'">').append(
         $('<td class="id">').text(name.id),
         $('<td class="catalog-table-name">').text(name.name),
         $('<td class="desc">').text(name.description),
         $('<td class="credits">').text(name.credits)
         )
      );
   });
   $('#catalog-table').DataTable({
      scrollY:       75,
      deferRender:   true,
      scroller:      true
   });
}
function fillReqs(reqs,courses){
   var rawHtml = "";
   $.each(reqs.categories,function(id){
      rawHtml += "<h3>"+id+"</h3>";
      var courses = reqs.categories[id];
      rawHtml += "<ul>";
      $.each(courses.courses,function(index){
         rawHtml += "<li>";
         rawHtml += courses.courses[index];
         rawHtml += "</li>";
      });
      rawHtml += "</ul>";
   });
   $('#req-accordion').html(rawHtml);
   $('#req-accordion').accordion({
      collapsible:true,
      active:false
   });
}
function fillPlan(coursesTaken,catalogCourses){
   var schedule = {};
   var smallest = parseInt(coursesTaken[Object.keys(coursesTaken)[0]].year);
   var largest = smallest;
   $.each(coursesTaken,function(name){
      if(parseInt(coursesTaken[name].year)<smallest){
         smallest = parseInt(coursesTaken[name].year);
      }
      if(parseInt(coursesTaken[name].year)>largest && coursesTaken[name].term == "Fall"){
         largest = parseInt(coursesTaken[name].year);
      }
   });
   var i;
   for(i = smallest;i<=largest;i++){
      schedule[i] = {'Fall':{},'Spring':{},'Summer':{}};
   }

   $.each(coursesTaken,function(name){
      var yearToPutIn = parseInt(coursesTaken[name].year);
      if(coursesTaken[name].term != "Fall"){
         yearToPutIn = parseInt(coursesTaken[name].year) - 1;
      }
      schedule[yearToPutIn][coursesTaken[name].term][coursesTaken[name].id] = coursesTaken[name].id;
   }); 
   rawHtml = "";
   $.each(schedule,function(year){
      rawHtml += "<div class = \"year\">";
      $.each(schedule[year],function(semester){
         rawHtml += "<div class = \"semester sortable\">";
         rawHtml += "<div class = \"ape-semester-header\">";
         if(semester == "Fall"){
            rawHtml += semester +" "+year;
         }else{
            rawHtml += semester +" "+(parseInt(year)+1);
         }
         rawHtml += "</div>";
         $.each(schedule[year][semester],function(classID){
            rawHtml += "<div class= \"ape-class\">";
            rawHtml += schedule[year][semester][classID] +" "+ catalogCourses[classID].name;
            rawHtml += "</div>";
         });
         rawHtml += "</div>";
      });
      rawHtml += "</div>";
   });
   $('#ape-plan-content').html(rawHtml);
   //$('.semester').sortable();
   //$('.ape-class').draggable();
}
$(document).ready(function(){
var buttonData;
    $.ajax({
      url: "http://judah.cedarville.edu/~gallaghd/cs3220/termProject/getCombined.php",
      type: 'GET',
      dataType:'json'
    }).success( function(data){
      var plan = data.plan;
	   $('#info-name').html('Name: ' + plan.student);
	   $('#info-major').html('Major: ' + plan.major);
	   $('#info-plan-name').html('Plan Name: ' + plan.name);
	   $('#info-cat').html('Catalog Year: ' + plan.catYear);
	   $('#info-curr-sem').html("Current Semester: " + plan.currTerm + plan.currYear);
	   var coursesTaken = data.plan.courses;
	   var catalog = data.catalog;
	   fillPlan(coursesTaken,catalog.courses);
	   fillTable(catalog);
	   $.ajax({
         url: "http://judah.cedarville.edu/~gallaghd/cs3220/termProject/getRequirements.php",
         type: 'GET',
         dataType:'json'
       }).success( function(data){
	      fillReqs(data,catalog.courses);
      });
      $.ajax({
         url: "http://judah.cedarville.edu/~gallaghd/ymm/ymmdb.php",
         type: 'GET',
         dataType:'json'
       }).success( function(d){
         var options = "";
	      $.each(d, function(index){
	         options += "<option>";
	         options += d[index];
	         options += "</option>";
	      });
	      $('#yearList').append(options);
      });
   });
   
    
   $('#logout').click(function() {
       window.location.replace("../cs3220.html");          
   }) 
   $('#peoples-choice').click(function(){
      window.location.replace("http://judah.cedarville.edu/index.php");
   })
   $('#yearList').change(function(){
      var selected = $(this).children("option:selected").val();
      var newUrl = "http://judah.cedarville.edu/~gallaghd/ymm/ymmdb.php?fmt=json&year="+selected;
      $.ajax({
         url: newUrl,
         type: 'GET',
         dataType:'json'
       }).success( function(d){
         var options = "";
	      $.each(d, function(index){
	         options += '<option id="'+d[index].id+'">';
	         options += d[index].name;
	         options += "</option>";
	      });
	      
	      $('#makeList').append(options);
	      //This don't work yet but Im getting there.
	      $('#makeList').prop('selected',function(){
	         return this.defaultSelected;
	      });
      });
   });
   $('#makeList').change(function(){
      var selected = $(this).children("option:selected")[0].id;
      var year = $('#yearList').children("option:selected").val();
      var newUrl = "http://judah.cedarville.edu/~gallaghd/ymm/ymmdb.php?fmt=json&year="+year+"&make="+selected;
      $.ajax({
         url: newUrl,
         type: 'GET',
         dataType:'json'
       }).success( function(d){
         var options = "";
	      $.each(d, function(index){
	         options += "<option>";
	         options += d[index].name;
	         options += "</option>";
	      });
	      $('#modelList').append(options);
      });
   });
   $('#carButton').click(function(){
      var message = "Thank you for selecting ";
      message += $('#yearList').children("option:selected").val() + " ";
      if($('#yearList').children("option:selected").val() == "select"){
         return;
      }
      message += $('#makeList').children("option:selected").val() + " ";
      if($('#makeList').children("option:selected").val() == "select"){
         return;
      }
      message += $('#modelList').children("option:selected").val() + ". ";
      if($('#modelList').children("option:selected").val() == "select"){
         return;
      }
      message += "Unfortuneatly, the website is down...";
      alert(message);
   });
})
