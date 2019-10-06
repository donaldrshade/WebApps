<?php
   ini_set('display_errors', 'On');
   error_reporting(E_ALL);
   session_start();

   function debugToConsole($msg) { 
        echo "<script>console.log(".json_encode($msg).")</script>";
   }
   
   $projectNumber = $_POST['projectToView'];
   
    debugToConsole($_SERVER['REQUEST_METHOD']);
    $db = mysqli_connect("james.cedarville.edu","cs3220","","cs3220_Sp19") or die("Error: unable to connect to database");
?>
<!DOCTYPE html>
<html>
    <link rel="stylesheet" href="Resources/pcCSS.css" type="text/css">
    <head>
        <meta charset="UTF-8" content="NO-CACHE">
        <title>People's Choice Awards</title>
        <script src="http://code.jquery.com/jquery-1.7.1.min.js"></script> 
        <!--<script src ="js/view.js"></script> -->   
        <script type="text/javascript" src="https://canvasjs.com/assets/script/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="https://canvasjs.com/assets/script/jquery.canvasjs.min.js"></script> 

    </head>
    <body>
        <div class="banner">
            <img src="Resources/banner.png" id="welcome"/>
        </div>
            <div class ="view">
                <form method="post">
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
                    <input type="button" value="Home" class = "button" id="home" onclick="location='Project5.php'">
                    <input type="submit" value="View" class ="viewSubmit" id="view" action="view.php">
                </form>
            </div>
            <div class="graph">
            

            <div id="chartContainer" style="height: 370px; width: 90%; margin: auto">
            <?php
            
            $projectID = $_POST['projectToView']; 
            
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
            
            $query = $db->prepare('SELECT project_number FROM purpleBlob_project WHERE project_id =?') or die("BUST");
            $query->bind_param("i",$projectID);
            $query->execute();
            $query->store_result();
            $query->bind_result($projectNumber);
            $query->fetch();
            $query->close();
            
            $teamNames = array();
            $goldTeamVotes = array();
            $silverTeamVotes = array();
            $bronzeTeamVotes = array();
            foreach($teams as $team){
            $names = array();
            $query = $db->prepare('Select display_name from purpleBlob_user join purpleBlob_team on purpleBlob_user.user_id = purpleBlob_team.user_id  and team_id =? and project_id =?') or die("BUST");
            $query->bind_param("ii",$team,$projectID);
            $query->execute();
            $query->store_result();
            $query->bind_result($name);
            while($query->fetch()){
               $names[] = ($name);
            }
            $query->close(); 
            
            $teamNames[$team] = $names; 
            
            //get gold team votes
            $query = $db->prepare('SELECT count(vote_id) FROM purpleBlob_vote WHERE gold_team_id = ?') or die("BUST");
            $query->bind_param("i", $team);
            $query->execute();
            $query->store_result();
            $query->bind_result($goldVotes);
            $query->fetch();
            $query->close(); 
            $goldTeamVotes[$team] = ($goldVotes*3);
            
            //get silver team votes
            $query = $db->prepare('SELECT count(vote_id) FROM purpleBlob_vote WHERE silver_team_id = ?') or die("BUST");
            $query->bind_param("i", $team);
            $query->execute();
            $query->store_result();
            $query->bind_result($silverVotes);
            $query->fetch();
            $query->close(); 
            $silverTeamVotes[$team] = ($silverVotes*2);
            
            //get bronze team votes
            $query = $db->prepare('SELECT count(vote_id) FROM purpleBlob_vote WHERE bronze_team_id = ?') or die("BUST");
            $query->bind_param("i", $team);
            $query->execute();
            $query->store_result();
            $query->bind_result($bronzeVotes);
            $query->fetch();
            $query->close(); 
            $bronzeTeamVotes[$team] = $bronzeVotes;

            
            }
            
            
            

            $script = '';
            $script = '
            <script>
               
               $(document).ready( function () {
                 
                  var options = {
	                  animationEnabled: true,
	                  theme: "dark2",
	                  title:{
		                   text: "Project ' . ($projectNumber) . ' "  
	                  },
	                  axisY2:{
		                  lineThickness: 0				
	                  },
	                  toolTip: {
		                  shared: true
	                  },
	                  legend:{
		                  verticalAlign: "top",
		                  horizontalAlign: "center"
	                  },
	                  data: [
	                  {     
		                  type: "stackedBar",
		                  showInLegend: true,
		                  name: "Bronze",
		                  axisYType: "secondary",
		                  color: "#cd7f32",
		                  dataPoints: [
			                  ';
			                  
			                  foreach($teams as $id){
			                     $script .= '{ y: ';
			                     $script .= $bronzeTeamVotes[$id];
			                     $script .= ', label: "';
			                     
			                    foreach($teamNames as $team_id => $name){
			                   
			                        if($id == $team_id){
			                           foreach($name as $i){
			                              $script .= $i;
			                              $script .= ' ';
			                           }
			                           
			                           break;
			                        }
			                    }
			                    
			                    $script .= '" },
			                    ';
			                  }
			                  
		                $script .=  ']
	                  },
	                  {
		                  type: "stackedBar",
		                  showInLegend: true,
		                  name: "Silver",
		                  axisYType: "secondary",
		                  color: "#C0C0C0",
		                  dataPoints: [ ';
		                  
			                  foreach($teams as $id){
			                     $script .= '{ y: ';
			                     $script .= $silverTeamVotes[$id];
			                     $script .= ', label: "';
			                    foreach($teamNames as $team_id => $name){
			                   
			                        if($id == $team_id){
			                           foreach($name as $i){
			                              $script .= $i;
			                              $script .= ' ';
			                           }
			                           
			                           break;
			                        }
			                    }
			                    
			                    $script .= '"},
			                    ';
			                  }
			               $script .= '
		                  ]
	                  },
	                  {
		                  type: "stackedBar",
		                  showInLegend: true,
		                  name: "Gold",
		                  axisYType: "secondary",
		                  color: "#FFD700",
		                  dataPoints: [ ';
		                  
			                  foreach($teams as $id){
			                     $script .= '{ y: ';
			                     $script .= $goldTeamVotes[$id];
			                     $script .= ', label: "';
			                    foreach($teamNames as $team_id => $name){
			                   
			                        if($id == $team_id){
			                           foreach($name as $i){
			                              $script .= $i;
			                              $script .= ' ';
			                           }
			                           
			                           break;
			                        }
			                    }
			                    
			                    $script .= '"},
			                    ';
			                  }
			               $script .= '
			                  ]
	                  },
	
	                  ]
                  };

                  $("#chartContainer").CanvasJSChart(options);
               });

            </script>
            ';
            echo $script;
            ?>
            </div>
            
            </div>
            <div class="write-ins">
                  <h3 class= "write-in-head">Write-ins</h3>
                  <?php
                  /*
                     $query = $db->prepare('SELECT user_id FROM purpleBlob_user WHERE username = ?') or die("BUST");
                     $query->bind_param("i", $_SESSION["PCusername"];);
                     $query->execute();
                     $query->store_result();
                     $query->bind_result($user_id);
                     $query->fetch();
                  
                     $query = $db->prepare('SELECT team_id FROM purpleBlob_team WHERE project_id = ? and user_id = ?') or die("BUST");
                     $query->bind_param("ii", $projectID, $user_id);
                     $query->execute();
                     $query->store_result();
                     $query->bind_result($;
                     */
                     
                     $query = $db->prepare('SELECT team_id,value FROM purpleBlob_writeIn WHERE project_id = ?') or die("BUST");
                     $query->bind_param("i", $projectID);
                     $query->execute();
                     $query->store_result();
                     $query->bind_result($ID,$val);
                     while($query->fetch()){
                        $script = '<p1 >';
                        foreach($teamNames as $team_id => $name){
                           if($ID == $team_id){
                              foreach($name as $n){
			                              $script .= $n;
			                              $script .= ' ';
			                     }
                           }
                        }
                       $script .= ' : ';
                        $script .= $val;
                        $script .= '</p1> </br>';
                        echo $script;
                     }
                     $query->close();
                     
                   ?>
                   
            </div>
        </div>
    </body>
</html>
