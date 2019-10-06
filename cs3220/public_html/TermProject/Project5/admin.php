<?php
   //ini_set('display_errors', 'On');
   //error_reporting(E_ALL);
   session_start();
   if(!isset($_SESSION["PCusername"])){
      header("Location: Project5.php");
   }
   $username = $_SESSION["PCusername"];
   if(isset($_POST['clear_session'])){
      $_SESSION = array();
      header("Location: Project5.php");
   }
   
   function debugToConsole($msg) { 
        echo "<script>console.log(".json_encode($msg).")</script>";
   }

    debugToConsole($_SERVER['REQUEST_METHOD']);
    debugToConsole($_SESSION);
    //debugToConsole($_POST);
    $db = mysqli_connect("james.cedarville.edu","cs3220","","cs3220_Sp19") or die("Error: unable to connect to database");
?>
<!DOCTYPE html>
<html>
    <link rel="stylesheet" href="Resources/pcCSS.css" type="text/css">
    <head>
        <meta charset="UTF-8" content="NO-CACHE">
        <title>People's Choice Awards</title>
        <script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
        <script src="js/pcc.js"></script>
        <script src="js/admin.js"></script>
    </head>
    <body>
        <div class="banner">
            <img src="Resources/banner.png" id="welcome"/>
        </div>
        <div class="greeting">Hello, <?php echo $username ?></div>
        <div class="interactionButtons">
            <div class="login">
                <?php
                     echo '<form action="admin.php" method="POST"><input type="hidden" name="clear_session" value="true"><input type="submit" value="Logout" class = "button"></form>';
                     echo '<form action="Project5.php" method="POST"><input type="submit" value="Home" class = "button"></form>';
                ?>
            </div>
            <div class ="view">
                <form action="none">
                    <select id="projectView"></select>
                    <input type="button" value="View" class ="button" id="view">
                </form>
            </div>
        </div>
        <div class="admin-content">
            <div id="interactables">
               <div class="settings-button-selected" id="manageProjectsTitle">Manage Projects</div>
               <div class="settings-button" id="modifyExistingUsersTitle">Modify Existing Users</div>
               <div class="settings-button" id="manageTeamsTitle">Manage Teams</div>
               <div class="settings-button" id="createNewUsersTitle">Create New Users</div>
               <div class="settings-button" id="manageClassesTitle">Manage Classes</div>
            </div>
            <div id="settings-content">
               <div id="manageProjects" class="slider">
                  <?php
                     //get the not opened projects
                     $test = $db->prepare('SELECT project_number FROM purpleBlob_project WHERE status="N"');
                     $test->execute();
                     $test->store_result();
                     $test->bind_result($projectnumber);
                     if($test->fetch()){
                        echo '<div><form action="settingsAction.php" method="POST"><input type="hidden" name="action" value="openProject">Open Projects: <select name="notopenedProject">';
                        $projects = $db->prepare('SELECT project_id,project_number FROM purpleBlob_project WHERE status="N"');
                        $projects->execute();
                        $projects->store_result();
                        $projects->bind_result($projectID,$projectnumber);
                        while($projects->fetch()){
                           echo "<option value=".$projectID.">Project ".$projectnumber."</option>";
                        }
                        echo '</select> <input type="submit" value="Open"></form></div>';
                     }else{
                        echo '<div>There are no projects to open.</div>';
                     } 
                     //get opened projects
                     $test = $db->prepare('SELECT project_number FROM purpleBlob_project WHERE status="O"');
                     $test->execute();
                     $test->store_result();
                     $test->bind_result($projectnumber);
                     if($test->fetch()){
                        echo '<div><form action="settingsAction.php" method="POST"><input type="hidden" name="action" value="closeProject">Close Projects: <select name="openedProject">';
                        $projects = $db->prepare('SELECT project_id,project_number FROM purpleBlob_project WHERE status="O"');
                        $projects->execute();
                        $projects->store_result();
                        $projects->bind_result($projectID,$projectnumber);
                        while($projects->fetch()){
                           echo "<option value=".$projectID.">Project ".$projectnumber."</option>";
                        }
                        echo '</select> <input type="submit" value = "Close"></form></div>';
                     }else{
                        echo '<div>There are no projects to close.</div>';
                     } 
                     //get the closed projects
                     $test = $db->prepare('SELECT project_number FROM purpleBlob_project WHERE status="C"');
                     $test->execute();
                     $test->store_result();
                     $test->bind_result($projectnumber);
                     if($test->fetch()){
                        echo '<div><form action="settingsAction.php" method="POST"><input type="hidden" name="action" value="reopenProject">Re-open Projects: <select name="closedProject">';
                        $projects = $db->prepare('SELECT project_id,project_number FROM purpleBlob_project WHERE status="C"');
                        $projects->execute();
                        $projects->store_result();
                        $projects->bind_result($projectID,$projectnumber);
                        while($projects->fetch()){
                           echo "<option value=".$projectID.">Project ".$projectnumber."</option>";
                        }
                        echo '</select> <input type="submit" value ="Re-open"></form></div>';
                     }else{
                        echo '<div>There are no projects to re-open.</div>';
                     } 
                  ?>
               </div>
               <div id="modifyExistingUsers" class="slider hidden">
                  <?php
                     echo '<table><tbody>';
                     $users = $db->prepare('SELECT user_id,display_name FROM purpleBlob_user');
                     $users->execute();
                     $users->store_result();
                     $users->bind_result($userID,$name);
                     while($users->fetch()){
                        echo '<tr><td>'.$name,'</td>';
                        echo '<td><form action="settingsAction.php" method="POST">';
                        echo '<input type=hidden name="ID" value='.$userID.'><input type=hidden name="action" value="resetPassword"><input type="submit" value="Reset Pasword"></form></td>';
                        if($name != "Admin"){
                           echo '<td><form action="settingsAction.php" method="POST">';
                           echo '<input type=hidden name="ID" value='.$userID.'><input type=hidden name="action" value="deleteUser"><input type="submit" value="Delete User"></form></td>';
                        }
                        echo "</tr>";
                     }
                     echo '</tbody></table>';
                  ?>
               </div>
               <div id="manageTeams" class="slider hidden">
                  Select Project to View: <select id="projects">
                  <?php
                     $projects = $db->prepare('SELECT project_id,project_number FROM purpleBlob_project WHERE status = "N"') or die("BUST TEMP");;
                     $projects->execute();
                     $projects->store_result();
                     $projects->bind_result($projectID,$projectNum);
                     $firstTry = true;
                     $first = 0;
                     while($projects->fetch()){
                        if($firstTry ==true){
                           $first = $projectID;
                           debugToConsole($projectNum);
                        }
                        echo "<option value=".$projectID.">Project ".$projectNum."</option>";
                        $firstTry = false;
                     }
                     $projects->close();
                  ?>
                  </select><br>
                  
                  Select Team: <select id="teams">
                  <?php
                     $teams = $db->prepare('SELECT DISTINCT team_id,user_id FROM purpleBlob_team WHERE project_id = ?') or die("BUST TEMP2");
                     $teams->bind_param("i",$first);
                     $teams->execute();
                     $teams->store_result();
                     $teams->bind_result($teamID,$UID);
                     $firstTry2 = true;
                     $firstTeam = 0;
                     while($teams->fetch()){
                        if($firstTry2 ==true){
                           $firstTeam = $teamID;
                           
                        }
                        $player = $db->prepare('SELECT display_name FROM purpleBlob_user WHERE user_id = ?') or die("BUST TEMP3");
                        $player->bind_param("i",$UID);
                        $player->execute();
                        $player->store_result();
                        $player->bind_result($dispName);
                        $player->fetch();
                        echo "<option value=".$teamID.">".$dispName." Team</option>";
                        $firstTry2 = false;
                     }
                     $teams->close();
                  ?>
                  </select>
                  <h3>Team Members:</h3>
                  <?php
                     $teamMember = $db->prepare('SELECT user_id FROM purpleBlob_team WHERE project_id = ? AND team_id=?') or die("BUST TEMP4");
                     $teamMember->bind_param("ii",$first,$firstTeam);
                     $teamMember->execute();
                     $teamMember->store_result();
                     $teamMember->bind_result($UID);
                     echo "<div id='members'>";
                     while($teamMember->fetch()){
                        $player = $db->prepare('SELECT display_name FROM purpleBlob_user WHERE user_id = ?') or die("BUST TEMP5");
                        $player->bind_param("i",$UID);
                        $player->execute();
                        $player->store_result();
                        $player->bind_result($dispName);
                        $player->fetch();
                        echo $dispName."<br>";
                     }
                     echo "</div>";
                  ?>
                  <h3>Add Member:</h3>
                  <?php
                     $teamMember = $db->prepare('SELECT user_id FROM purpleBlob_user WHERE user_id NOT IN (SELECT user_id FROM purpleBlob_team WHERE project_id = ? AND team_id=?) AND user_id <> 1') or die("BUST TEMP5");
                     $teamMember->bind_param("ii",$first,$firstTeam);
                     $teamMember->execute();
                     $teamMember->store_result();
                     $teamMember->bind_result($UID);
                     echo '<select id="userToAdd">';
                     while($teamMember->fetch()){
                        $player = $db->prepare('SELECT display_name FROM purpleBlob_user WHERE user_id = ?') or die("BUST TEMP5");
                        $player->bind_param("i",$UID);
                        $player->execute();
                        $player->store_result();
                        $player->bind_result($dispName);
                        $player->fetch();
                        echo "<option value=".$UID.">".$dispName."</option>";
                     }
                     echo "</select><button type='button' id='addMember'>Add To Team</button>";
                  ?>
               </div>
               <div id="createNewUsers" class="slider hidden">
                  <form action="settingsAction.php" method="POST">
                     <input type="hidden" name="action" value="newUsers">
                     <table id="newUsers">
                        <thead>
                           <tr>
                              <th>Display Name</th>
                              <th>Username</th>
                              <th>URL Tag</th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr>
                              <td><input type="text" name="display_name1"></td>
                              <td><input type="text" name="username1"></td>
                              <td><input type="text" name="url_tag1"></td>
                           </tr>
                        </tbody>
                     </table>
                     <button type=button id="anotherUser">Add Another User</button>
                     <br>
                     <input type="submit" value="Add Users">
                  </form>
               </div>
               <div id="manageClasses" class="slider hidden">
                  <form action="settingsAction.php" method="POST">
                     This will currently reset People's Choice and delete all user accounts except for Admin.<br><br>
                     <input type="hidden" name="action" value="newClass">
                     Number of Projects: <input type="text" name="newProjects" value ="0"><br><br>
                     <input type="submit" value="Reset People's Choice">
                  </form>
               </div>
            </div>
        </div>
        
    </body>
    <?php
      mysqli_close($db);
    ?>
</html>
