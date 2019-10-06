function fillCatalog(catalog){
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
   if(coursesTaken.length<1){
      return;
   }
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
   var totalCredits = 0;
   rawHtml = "";
   $.each(schedule,function(year){
      rawHtml += "<div class = \"year\">";
      $.each(schedule[year],function(semester){
         var creditLoad = 0;
         var body = "";
         $.each(schedule[year][semester],function(classID){
            body += "<div class= \"ape-class\">";
            body += schedule[year][semester][classID] +" "+ catalogCourses[classID].name;
            body += "</div>";
            creditLoad += catalogCourses[classID].credits;
            totalCredits += catalogCourses[classID].credits;
         });
         var header ="<div class = \"semester sortable\">";
         header += "<div class = \"ape-header-encapsulate\">";
         header += "<div class = \"ape-header-credits\"></div>";
         
         
         header += "<div class = \"ape-semester-header\">";
         if(semester == "Fall"){
            header += semester +" "+year;
         }else{
            header += semester +" "+(parseInt(year)+1);
         }
         header += "</div>";
         header += "<div class = \"ape-header-credits\">"+creditLoad+"</div>";
         header += "</div>"
         rawHtml += header + body + "</div>";
      });
      rawHtml += "</div>";
   });
   $('#ape-plan-content').html(rawHtml);
   $('#credits').html("Credits: "+totalCredits+" hours");
}
$(document).ready(function(){
var buttonData;
var user = $('#user').html();
var url = "getData.php?user="+user.trim();
    $.ajax({
      url: url,
      type: 'GET',
      dataType:'json'
    }).success( function(data){
      console.log(data);
      setScreen(data,false);
   });
   $('#info-plans').change(function(){
      var selected = $(this).children("option:selected").val();
      $.ajax({
         url: url+"&&plan_name="+selected.trim(),
         type: 'GET',
         dataType:'json'
      }).success( function(data){
         //reset accordion
         $('#ape-upper-left').html('<h2>Plan Requirements</h2><div id = "req-accordion"></div>');
         //reset table
         var rawInsert = '<table id = "catalog-table">'+"<thead><tr>";
         rawInsert += '<th class = "id">Course ID</th>';
         rawInsert += '<th class = "catalog-table-name">Title</th>';
         rawInsert += '<th class = "desc">Description</th>';
         rawInsert += '<th class = "credits">Credits</th>';             
         rawInsert += "</tr></thead></table>";             
         $('#myTable').html(rawInsert);
         $('#ape-plan-content').html("");
         setScreen(data);
      });
   });
    
   $('#logout').click(function() {
      window.location.replace("login.php");
   }) 
   $('#peoples-choice').click(function(){
      window.location.replace("http://judah.cedarville.edu/index.php");
   })
})
function setScreen(data){
   console.log(data);
   var plan = data.plan;
   $('#info-name').html('Name: ' + plan.student);
   $('#info-major').html('Major: ' + plan.major);
   $('#plan_name').html('Academic Plan: ' + plan.name);
   $('#cat_year').html('Catalog Year : ' + plan.catYear);
   $('#info-curr-sem').html("Current Semester: " + plan.currTerm + plan.currYear);
   var coursesTaken = plan.courses;
   var catalog = data.catalog;
   var reqs = data.reqs;
   fillPlan(coursesTaken,catalog.courses,plan.name);
   fillCatalog(catalog);
   fillReqs(data.requirements,catalog.courses);
   console.log(data.plan_names);
   $('#info-plans').html("<option value="+data.plan.name+">"+data.plan.name+"</option>");
   $.each(data.plan_names,function(i){
      if(data.plan_names[i] != data.plan.name){
         var innerhtml = $('#info-plans').html();
         innerhtml += "<option value="+data.plan_names[i]+">"+data.plan_names[i]+"</option>";
         $('#info-plans').html(innerhtml);
      }
   });
}
