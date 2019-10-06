<?php
   ini_set('display_errors', 'On');
   error_reporting(E_ALL);
   if($_SERVER["REQUEST_METHOD"]=="GET"){
      if(isset($_GET["userToAdd"])){
         //Init DB
         $db = mysqli_connect("james.cedarville.edu","cs3220","","cs3220_Sp19") or die("Error: unable to connect to database");
         //Update the team
         $newTeam = $db->prepare('UPDATE purpleBlob_team SET team_id = ? WHERE user_id=? AND project_id=?') or die("BUST");
         $newTeam->bind_param("iii",$_GET["team"],$_GET["userToAdd"],$_GET["project"]);
         $newTeam->execute();
         $newTeam->close();
         mysqli_close($db);
         //Now get new data
         $project = $_GET["project"];
         $compiled = array();
         $compiled['test'] = "5";
         //Get Teams
         $team = getTeamList($compiled,$project);
         //TeamList is now set and we have gathered the first Team
         getMembers($compiled,$team,$project);
         //Non-Team Members
         getNonTeamMembers($compiled,$team,$project);
         mysqli_close($db);
         echo json_encode($compiled);
      }else if(isset($_GET["team"])){
         $team = $_GET["team"];
         $project = $_GET["project"];
         $compiled = array();
         getMembers($compiled,$team,$project);
         getNonTeamMembers($compiled,$team,$project);
         echo json_encode($compiled);
      }else if(isset($_GET["project"])){
         $project = $_GET["project"];
         $compiled = array();
         $team = getTeams($compiled,$project);
         //TeamList is now set and we have gathered the team
         getMembers($compiled,$team,$project);
         //Non-Team Members
         getNonTeamMembers($compiled,$team,$project);
         echo json_encode($compiled);
      }else{
         header("Location: Project5.php");
      }
   }else{
      header("Location: Project5.php");
   }
   function getNonTeamMembers(&$compiled,$teamID,$projectID){
      $db = mysqli_connect("james.cedarville.edu","cs3220","","cs3220_Sp19") or die("Error: unable to connect to database");
      $teamMember = $db->prepare('SELECT user_id FROM purpleBlob_user WHERE user_id NOT IN (SELECT user_id FROM purpleBlob_team WHERE project_id = ? AND team_id=?) AND user_id <> 1') or die("BUST TEMP5");
      $teamMember->bind_param("ii",$projectID,$teamID);
      $teamMember->execute();
      $teamMember->store_result();
      $teamMember->bind_result($UID);
      $nonMembersID = array();
      $nonMembersName = array();
      while($teamMember->fetch()){
         $player = $db->prepare('SELECT display_name FROM purpleBlob_user WHERE user_id = ?') or die("BUST TEMP5");
         $player->bind_param("i",$UID);
         $player->execute();
         $player->store_result();
         $player->bind_result($dispName);
         $player->fetch();
         $nonMemberID[] = $UID;
         $nonMemberName[] = $dispName;
      }
      $compiled["nonMemberName"] = $nonMemberName;
      $compiled["nonMemberID"] = $nonMemberID;
      mysqli_close($db);
   }
   function getTeams(&$compiled,$project){
      $db = mysqli_connect("james.cedarville.edu","cs3220","","cs3220_Sp19") or die("Error: unable to connect to database");
      $teamList = array();
      $ID = array();
      $teams = $db->prepare('SELECT DISTINCT team_id,user_id FROM purpleBlob_team WHERE project_id = ?') or die("BUST TEMP2");
      $teams->bind_param("i",$project);
      $teams->execute();
      $teams->store_result();
      $teams->bind_result($teamID,$UID);
      $firstTry = true;
      $team = 0;
      while($teams->fetch()){
         if($firstTry ==true){
            $team = $teamID;
         }
         $ID[] = $teamID;
         $player = $db->prepare('SELECT display_name FROM purpleBlob_user WHERE user_id = ?') or die("BUST TEMP3");
         $player->bind_param("i",$UID);
         $player->execute();
         $player->store_result();
         $player->bind_result($dispName);
         $player->fetch();
         $teamList[] = $dispName." Team";
         $firstTry = false;
      }
      $teams->close();
      $compiled["ID"] = $ID;
      $compiled["team"] = $teamList;
      mysqli_close($db);
      return $team;
   }
   function getMembers(&$compiled,$team,$project){
      $db = mysqli_connect("james.cedarville.edu","cs3220","","cs3220_Sp19") or die("Error: unable to connect to database");
      $memberList = array();
      $teamMember = $db->prepare('SELECT user_id FROM purpleBlob_team WHERE project_id = ? AND team_id=?') or die("BUST TEMP4");
      $teamMember->bind_param("ii",$project,$team);
      $teamMember->execute();
      $teamMember->store_result();
      $teamMember->bind_result($UID);
      while($teamMember->fetch()){
         $player = $db->prepare('SELECT display_name FROM purpleBlob_user WHERE user_id = ?') or die("BUST TEMP5");
         $player->bind_param("i",$UID);
         $player->execute();
         $player->store_result();
         $player->bind_result($dispName);
         $player->fetch();
         $memberList[] = $dispName;
      }
      $compiled["member"] = $memberList;
      mysqli_close($db);
   }
?>
