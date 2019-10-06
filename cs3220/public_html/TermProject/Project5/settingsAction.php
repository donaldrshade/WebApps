<?php
   ini_set('display_errors', 1);
   ini_set('display_startup_errors', 1);
   error_reporting(E_ALL);
   session_start();
   debugToConsole($_SERVER['REQUEST_METHOD']);
   debugToConsole($_SESSION);
   debugToConsole($_POST);
   function debugToConsole($msg) { 
        echo "<script>console.log(".json_encode($msg).")</script>";
   }
   if($_SERVER["REQUEST_METHOD"]=="POST") {
      if(isset($_POST["action"])){
         $db = mysqli_connect("james.cedarville.edu","cs3220","","cs3220_Sp19") or die("Error: unable to connect to database");
         //echo $_POST["action"];
         if($_POST["action"]=="openProject"){
            $projectID = $_POST["notopenedProject"];
            $query = $db->prepare("UPDATE purpleBlob_project SET status = 'O' WHERE project_id=? ") or die("BUST");
            $query->bind_param("i",$projectID);
            $query->execute();
            $query->close();
            
            runClose();
         }else if($_POST["action"]=="closeProject"){
            $projectID = $_POST["openedProject"];
            $query = $db->prepare("UPDATE purpleBlob_project SET status = 'C' WHERE project_id=? ") or die("BUST");
            $query->bind_param("i",$projectID);
            $query->execute();
            $query->close();
            //insert code for awards here
            $scoring = $db->prepare("SELECT gold_team_id,silver_team_id,bronze_team_id FROM purpleBlob_vote WHERE project_id=? ") or die("BUST12");
            $scoring->bind_param("i",$projectID);
            $scoring->execute();
            $scoring->store_result();
            $scoring->bind_result($gold,$silver,$bronze);
            $points = array();
            while($scoring->fetch()){
               if(!isset($points[$gold])){
                  $points[$gold] = 3;
               }else{
                  $points[$gold] += 3;
               }
               if(!isset($points[$silver])){
                  $points[$silver] = 2;
               }else{
                  $points[$silver] += 2;
               }
               if(!isset($points[$bronze])){
                  $points[$bronze] = 1;
               }else{
                  $points[$bronze] += 1;
               }
               $goldMax = 0;
               $goldKey = "";
               //find max
               foreach($points as $key => $value){
                  if($value > $goldMax){
                     $goldMax = $value;
                     $goldKey = $key;
                  }
               }
               //find second max
               $silverMax = 0;
               $silverKey = "";
               foreach($points as $key => $value){
                  if($value > $max && $key != $goldKey){
                     $silverMax = $value;
                     $silverKey = $key;
                  }
               }
               //find third
               $bronzeMax = 0;
               $bronzeKey = "";
               foreach($points as $key => $value){
                  if($value > $max && $key != $goldKey && $key != $silverKey){
                     $bronzeMax = $value;
                     $bronzeKey = $key;
                  }
               }
               $query = $db->prepare("SELECT user_id FROM purpleBlob_team WHERE project_id=? AND team_id=? ") or die("BUST1");
               $query->bind_param("ii",$projectID,$goldKey);
               $query->execute();
               $query->store_result();
               $query->bind_result($UID);
               while($query->fetch()){
                  $score = $db->prepare("UPDATE purpleBlob_award SET award='G' WHERE project_id=? AND user_id=? ") or die("BUST2");
                  $score->bind_param("ii",$projectID,$UID);
                  $score->execute();
                  $score->close();
               }
               $query->close();
               $query = $db->prepare("SELECT user_id FROM purpleBlob_team WHERE project_id=? AND team_id=? ") or die("BUST3");
               $query->bind_param("ii",$projectID,$silverKey);
               $query->execute();
               $query->store_result();
               $query->bind_result($UID);
               while($query->fetch()){
                  $score = $db->prepare("UPDATE purpleBlob_award SET award='S' WHERE project_id=? AND user_id=? ") or die("BUST4");
                  $score->bind_param("ii",$projectID,$UID);
                  $score->execute();
                  $score->close();
               }
               $query->close();
               $query = $db->prepare("SELECT user_id FROM purpleBlob_team WHERE project_id=? AND team_id=? ") or die("BUST5");
               $query->bind_param("ii",$projectID,$bronzeKey);
               $query->execute();
               $query->store_result();
               $query->bind_result($UID);
               while($query->fetch()){
                  $score = $db->prepare("UPDATE purpleBlob_award SET award='B' WHERE project_id=? AND user_id=? ") or die("BUST6");
                  $score->bind_param("ii",$projectID,$UID);
                  $score->execute();
                  $score->close();
               }
               $query->close();
            }
            $scoring->close();
            //Here add code for assigning awards and write-in awards
            $writein = $db->prepare("SELECT user_id FROM purpleBlob_award WHERE project_id=? AND award='N' ") or die("BUST7");
            $writein->bind_param("i",$projectID);
            $writein->execute();
            $writein->store_result();
            $writein->bind_result($UID);
            while($writein->fetch()){
               $writeinJoin = $db->prepare("SELECT value FROM purpleBlob_writeIn a LEFT JOIN purpleBlob_team b on a.project_id = b.project_id AND a.team_id =b.team_id WHERE a.project_id=? AND user_id=? ") or die("BUST8");
               $writeinJoin->bind_param("ii",$projectID,$UID);
               $writeinJoin->execute();
               if($writeinJoin->fetch()){
                  $update = $db->prepare("UPDATE purpleBlob_award SET award='W' WHERE project_id=? and user_id=?") or die("BUST9");
                  $update->bind_param("ii",$projectID,$UID);
                  $update->execute();
                  $update->close();
               }
               $writeinJoin->close();
            }
            
            runClose();
         }else if($_POST["action"]=="reopenProject"){
            $projectID = $_POST["closedProject"];
            $query = $db->prepare("UPDATE purpleBlob_project SET status = 'O' WHERE project_id=? ") or die("BUST");
            $query->bind_param("i",$projectID);
            $query->execute();
            $query->close();
            //insert code for awards here
            $query = $db->prepare("UPDATE purpleBlob_award SET award = 'N' WHERE project_id=? ") or die("BUST");
            $query->bind_param("i",$projectID);
            $query->execute();
            $query->close();
            runClose();
         }else if($_POST["action"]=="resetPassword"){
            $userID = $_POST["ID"];
            $query = $db->prepare("SELECT username FROM purpleBlob_user WHERE user_id=?") or die("BUST");
            $query->bind_param("i",$userID);
            $query->execute();
            $query->store_result();
            $query->bind_result($username);
            $query->fetch();
            $query->close();
            $hash = hash("md5",$username."password");
            $query = $db->prepare("UPDATE purpleBlob_user SET password = '".$hash."' WHERE user_id=?") or die("BUST"); 
            $query->bind_param("i",$userID);
            $query->execute();
            $query->close();
            echo "made it";
            runClose();
         }else if($_POST["action"]=="deleteUser"){
            $userID = $_POST["ID"];
            $query = $db->prepare("DELETE FROM purpleBlob_user WHERE user_id=?") or die("BUST");
            $query->bind_param("i",$userID);
            $query->execute();
            $query->close();
            runClose();
         }else if($_POST["action"]=="newUsers"){
            $myBool = true;
            $myInt = 1;
            $displays = array();
            $usernames = array();
            $urls = array();
            $hashes = array();
            while($myBool){
               if(isset($_POST["display_name".$myInt]) && isset($_POST["username".$myInt]) && isset($_POST["url_tag".$myInt])){
                  $displays[$myInt] = $_POST["display_name".$myInt];
                  $usernames[$myInt] = $_POST["username".$myInt];
                  $urls[$myInt] = $_POST["url_tag".$myInt];
                  $hashes[$myInt] = hash("md5",$usernames[$myInt]."password");
                  $myInt = $myInt + 1;
               }else{
                  $myBool = false;
               }

            }
            for($myInt = 1;$myInt <=count($usernames);$myInt++){
               $display = $displays[$myInt];
               $username = $usernames[$myInt];
               $url_tag = $urls[$myInt];
               $hash = $hashes[$myInt];
               $query = $db->prepare("INSERT INTO purpleBlob_user (display_name, username, url_tag, password) VALUES (? ,? ,? ,?)") or die("BUST1");
               $query->bind_param("ssss",$display,$username,$url_tag,$hash);
               $query->execute();
               $query->close();
               $query = $db->prepare("SELECT user_id FROM purpleBlob_user WHERE username=?") or die("BUST2");
               $query->bind_param("s",$username);
               $query->execute();
               $query->store_result();
               $query->bind_result($userID);
               $query->fetch();
               $query->close();
               $query = $db->prepare("SELECT project_id FROM purpleBlob_project WHERE section_id=1") or die("BUST3");
               $query->execute();
               $query->store_result();
               $query->bind_result($projectID);
               while($query->fetch()){
                  $insert = $db->prepare("INSERT INTO purpleBlob_award (project_id, user_id,award) VALUES (? ,? ,'N')") or die("BUST4");
                  $insert->bind_param("ii",$projectID,$userID);
                  $insert->execute();
                  $insert->close();
                  $insertTeam = $db->prepare("INSERT INTO purpleBlob_team (project_id, user_id) VALUES (?,?)") or die("BUST5");
                  $insertTeam->bind_param("ii",$projectID,$userID);
                  $insertTeam->execute();
                  $insertTeam->close();
               }
               $query->close();
               
            }
            runClose();
         }else if($_POST["action"]=="newClass"){
            debugToConsole((int)$_POST['newProjects']);
            if((int)$_POST['newProjects']<=0 || (int)$_POST['newProjects']>16){
               debugToConsole("true");
               runClose();
            }else{
               $query = $db->prepare("DELETE FROM purpleBlob_user WHERE username<>'Admin'") or die("BUST");
               $query->execute();
               $query->close();
               $query = $db->prepare("DELETE FROM purpleBlob_project WHERE section_id=1") or die("BUST");
               $query->execute();
               $query->close();
               $query = $db->prepare("DELETE FROM purpleBlob_team") or die("BUST");
               $query->execute();
               $query->close();
               $query = $db->prepare("DELETE FROM purpleBlob_vote") or die("BUST");
               $query->execute();
               $query->close();
               $query = $db->prepare("DELETE FROM purpleBlob_writeIn") or die("BUST");
               $query->execute();
               $query->close();
               $query = $db->prepare("DELETE FROM purpleBlob_award") or die("BUST");
               $query->execute();
               $query->close();
               for($i = 1;$i <= (int)$_POST['newProjects'];$i++){
                  $query = $db->prepare("INSERT INTO purpleBlob_project (project_number,section_id,status) VALUES (?,'1','N')") or die("BUST1");
                  $query->bind_param("i",$i) or die("BUST2");
                  $query->execute() or die("BUST3");
                  $query->close() or die("BUST4");            
               }
               runClose();
            }
         }else{
            mysqli_close($db);
            header("Location: Project5.php");
         }
      }
   }else{
      header("Location: Project5.php");
   }
   function runClose(){
      mysqli_close($db);
      header("Location: admin.php");
   }
?>
