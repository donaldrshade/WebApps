<?php
   ini_set('display_errors', 'On');
   error_reporting(E_ALL);
   session_start();

   function debugToConsole($msg) { 
        echo "<script>console.log(".json_encode($msg).")</script>";
   }
   $db = mysqli_connect("james.cedarville.edu","cs3220","","cs3220_Sp19") or die("Error: unable to connect to database");
   
  // $myName = $_SESSION["PCusername"];

?>


<!DOCTYPE html>
<html>
    <link rel="stylesheet" href="Resources/pcCSS.css" type="text/css">
    <head>
        <meta charset="UTF-8" content="NO-CACHE">
        <title>People's Choice Awards</title>
        <script src="http://code.jquery.com/jquery-1.7.1.min.js"></script> 
    </head>
    <body>
            <div class="banner">
               <img src="Resources/banner.png" id="welcome"/>
            </div>
            <div class ="view">
                <form method="post">
                    <input type="button" value="Home" class = "button" id="home" onclick="location='Project5.php'">
                    <select name="projectToView" id="projectView"> 
                    
                    <?php 
                        $query = $db->prepare('SELECT project_id,project_number FROM purpleBlob_project WHERE status="C"') or die("BUST");
                        $query->execute();
                        $query->store_result();
                        $query->bind_result($ID,$num);
                        while($query->fetch()){
                           echo '<option value = "'.$ID.'">Project'.$num.'</option>';
                        }
                        $query->close(); 
                        ?>
                    </select>
                    <input type="submit" value="View" class ="viewSubmit" id="view" action="view.php">
                </form>
            </div>
            
            <?php
            
                           $projectID = $_POST['projectToVote'];
                           //debugToConsole($projectID);
                           
                         //get all the teams
                           $teams = array();
                           $query = $db->prepare('SELECT team_id FROM purpleBlob_team WHERE project_id =?') or die("BUST");
                           $query->bind_param("i",$projectID);
                           $query->execute();
                           $query->store_result();
                           $query->bind_result($ID);
                           while($query->fetch()){
                              $teams[] = ($ID);
                           }
                           $query->close(); 
                           //debugToConsole($teams);
                           
                           $query = $db->prepare('SELECT project_number FROM purpleBlob_project WHERE project_id =?') or die("BUST");
                           $query->bind_param("i",$projectID);
                           $query->execute();
                           $query->store_result();
                           $query->bind_result($projectNumber);
                           $query->fetch();
                           $query->close();
                           
                           $teamNames = array();
                           foreach($teams as $team){
                           $names = array();
                           $query = $db->prepare('SELECT display_name from purpleBlob_user join purpleBlob_team on purpleBlob_user.user_id = purpleBlob_team.user_id  and team_id =? and project_id =?') or die("BUST");
                           $query->bind_param("ii",$team,$projectID);
                           $query->execute();
                           $query->store_result();
                           $query->bind_result($name);
                           while($query->fetch()){
                              $names[] = ($name);
                           }
                           $query->close(); 
                           
                           $teamNames[$team] = $names; 
                           }
                           
                           
            ?>
            
            
            <div class="vote">
            
               <h1>Vote</h1>
               <form method="POST" id="vote-submit" action="recordVotes.php">
                  <h3 id="gold-medal">Gold Medal:</h3>
                   <select name="gold" id="gold">
                     //get all open teams that that are not admin and not self
                     <?php
                           foreach($teams as $team){
                              //create an option foreach team
                              $echo = '<option value= "' . $team . '">';
                              foreach($teamNames as $team_id => $name){
                                 if($team == $team_id){
                                    foreach($name as $i){
                                       $echo .= $i;
                                       $echo .= ' ';
                                    }
                                 }
                              }
                              $echo .= '</option>';
                              echo $echo;
                              
                           }
                           
                       ?>
                   </select>
                  <h3 id="silver-medal">Silver Medal:</h3>
                  <select name="silver" id="silver">
                  <?php
                           foreach($teams as $team){
                              //create an option foreach team
                              $echo = '<option value= "' . $team . '">';
                              foreach($teamNames as $team_id => $name){
                                 if($team == $team_id){
                                    foreach($name as $i){
                                       $echo .= $i;
                                       $echo .= ' ';
                                    }
                                 }
                              }
                              $echo .= '</option>';
                              echo $echo;
                              
                           }
                           
                       ?>
                  </select>
                  <h3 id="bronze-medal">Bronze Medal:</h3>
                  <select name="bronze" id="bronze">
                  <?php
                          foreach($teams as $team){
                              //create an option foreach team
                              $echo = '<option value= "' . $team . '">';
                              foreach($teamNames as $team_id => $name){
                                 if($team == $team_id){
                                    foreach($name as $i){
                                       $echo .= $i;
                                       $echo .= ' ';
                                    }
                                 }
                              }
                              $echo .= '</option>';
                              echo $echo;
                           }
                           echo '</select';
                           
                           
                           
                           $_SESSION['projectId'] = $projectID;
                       ?>
                  
                  <p></p>
                  <h3>Write ins</h3>
                  <p></p>
                  <select name="vote_teams">
                  <?php
                          foreach($teams as $team){
                              //create an option foreach team
                              $echo = '<option value= "' . $team . '">';
                              foreach($teamNames as $team_id => $name){
                                 if($team == $team_id){
                                    foreach($name as $i){
                                       $echo .= $i;
                                       $echo .= ' ';
                                    }
                                 }
                              }
                              $echo .= '</option>';
                              echo $echo;
                           }
                           echo '</select>';
                  ?>
                  <input type="text" name="writeIn"></input>
                  <input type="button" value="Submit" onclick="submitClicked()"> 
               </form>
            </div>
               

                  <script>
                  
                     function submitClicked(){
                     //get my team and validate that I cant vote for myself
                        
                     
                        let goldTeam = document.getElementById("gold").value;
                        let silverTeam = document.getElementById("silver").value;
                        let bronzeTeam = document.getElementById("bronze").value;
                        if(goldTeam == silverTeam || goldTeam == bronzeTeam || silverTeam == bronzeTeam){
                              alert("You can not give the same team two medals");
                        }
                        else{
                           //document.getElementById("write-in-submit").submit();
                           document.getElementById("vote-submit").submit();
                           
                     }
                     }
                  </script>

            
    </body>
</html>

