<?php
   //ini_set('display_errors', 'On');
   //error_reporting(E_ALL);
   session_start();
    
   if($_SERVER["REQUEST_METHOD"]=="POST") {
      $user = $_POST["PCusername"];
      $pass = $_POST["PCpassword"];
      if(empty($user) or empty($pass)){
         $invalid = true;
         $_POST = array();//clear the data
      }
      else{
         $cred_user = "cs3220";
         $cred_pass = "";
         $db = mysqli_connect("james.cedarville.edu",$cred_user,$cred_pass,"cs3220_Sp19") or die("Error: unable to connect to database");
         $hashed_pass = hash("md5",$user.$pass);
         echo $hashed_pass;
         $db = mysqli_connect("james.cedarville.edu","cs3220","","cs3220_Sp19") or die("Error: unable to connect to database");
         $query = $db->prepare('SELECT user_id FROM purpleBlob_user WHERE username = ? AND password = ?') or die("BUST");
         $query->bind_param("ss",$user,$hashed_pass);
         $query->execute();
         $query->store_result();
         $query->bind_result($user_id);
         $query->fetch();
         $query->close();
         mysqli_close($db);
         if(!empty($user_id)){
            $_SESSION["PCusername"] = $user;
            
         }
         else{
            $invalid = true;
            $_POST = array();//clear the data
         }
      }
      if(isset($_POST['clear_session'])){
         $_SESSION = array();
         
      }
      //header("Location: Project5.php");
   }
   elseif($_SERVER["REQUEST_METHOD"]=="GET"){
      
   }

   function debugToConsole($msg) { 
        echo "<script>console.log(".json_encode($msg).")</script>";
   }
   
    
    if( isset($_SESSION["PCusername"]) && !empty($_SESSION["PCusername"])){
        //Here we are good to go and we remember from last time
        $username = $_SESSION["PCusername"];
    } 
    else{
        //this will set it to Guest if it has nothing else;
        $username = "Guest";
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
    </head>
    <body>
        <div class="banner">
            <img src="Resources/banner.png" id="welcome"/>
        </div>
        <div class="greeting">Hello, <?php echo $username ?></div>
        <div class="interactionButtons">
            <div class="login">
                <?php
                   if($username == "Guest"){
                     echo '<form method="POST">Username:<select name="PCusername">';
                     //Here we need to grab the user info.
                     $query = $db->prepare('SELECT username,display_name FROM purpleBlob_user');
                     $query->execute();
                     $query->store_result();
                     $query->bind_result($name,$display_name);
                     $usernames = array();
                     while($query->fetch()){
                        echo '<option value = "'.$name.'">'.$display_name.'</option>';
                     }
                     $query->close();
                     echo '</select>Password:<input type="text" name="PCpassword"><input type="submit" value="Login" class = "button"></form>';
                   }
                   else{
                     echo '<form action="admin.php" method="POST"><input type="hidden" name="clear_session" value="true"><input type="submit" value="Logout" class = "button"></form>';
                   }
                   
                   if($username == "Admin"){
                     echo '<form action="admin.php"method="POST"><input type="submit" value="Settings" class = "button"></form>';
                   }
                   
                   
                ?>
            </div>
            <div class ="view">
                <?php
                   if($username != "Guest" && $username != "Admin"){
                     echo '<form action="vote.php"method="POST"><select name="projectToVote">';
                     $query = $db->prepare('SELECT project_id,project_number FROM purpleBlob_project WHERE status="O"') or die("BUST");
                     $query->execute();
                     $query->store_result();
                     $query->bind_result($ID,$num);
                     while($query->fetch()){
                        echo '<option value = "'.$ID.'">Project'.$num.'</option>';
                     }
                     $query->close();
                     echo '</select><input type="submit" value="Vote" class = "button"></form>';
                   }
                ?>
                <form action="view.php" method="post">
                     <select name="projectToView">
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
                     </select><input type="submit" value="View" class ="button" id="view">
                </form>
            </div>
        </div>
        <div class ="content">
            <table class = "project-table">
               <thead>
                  <tr class = "table-head"><th>User</th>
                     <?php
                        $query = $db->prepare('SELECT COUNT(project_number) 
												FROM purpleBlob_project WHERE section_id = 1');
                        $query->execute();
                        $query->store_result();
                        $query->bind_result($numProjects);
                        $query->fetch();
                        
		                  for($i = 1; $i <= $numProjects; $i++) {
			                  echo '<th class="table-head"> Project ' . $i . '</th>';
		                  }
	                  ?>
                  </tr>
               </thead>
               <tbody>
                  <?php 
                     $query = $db->prepare('SELECT display_name,user_id,url_tag FROM purpleBlob_user WHERE username != "Admin"');
                     $query->execute();
                     $query->store_result();
                     $query->bind_result($name,$ID,$tag);
                     while($query->fetch()){
		                  echo '<tr><td class = "table-users"><a href="http://judah.cedarville.edu/~'.$tag.'/cs3220.html">' .$name . '</a></td>';
		                  for($i = 1; $i <= $numProjects; $i++){
		                     //TODO ADD code here to display medals
		                     $awardquery = $db->prepare('SELECT award FROM purpleBlob_award a LEFT JOIN purpleBlob_project b ON a.project_id = b.project_id WHERE a.user_id = ? AND project_number = ? ') or die(debugToConsole("WHAT?"));
		                     $awardquery->bind_param("ii",$ID,$i);
                           $awardquery->execute();
                           $awardquery->store_result();
                           $awardquery->bind_result($award);
                           $awardquery->fetch();
                           $awardquery->close();
                           if($award == 'G'){
                              echo '<td class = "table-users"><img src="Resources/G.png" width="50" height="50"></td>';
                           }else if($award == 'S'){
                              echo '<td class = "table-users"><img src="Resources/S.png" width="50" height="50"></td>';
                           }else if($award == 'B'){
                              echo '<td class = "table-users"><img src="Resources/B.png" width="50" height="50"></td>';
                           }else if($award == 'W'){
                              echo '<td class = "table-users"><img src="Resources/W.png" width="50" height="50"></td>';
                           }else{
                              echo '<td class = "table-users"></td>';
                           }
			               }
		                  echo '</tr>';
                     }
                     $query->close();
                  ?>
               </tbody>
             </table>
               
        </div>
    </body>
    <footer>
      <div class="footer-flex"><a href="http://judah.cedarville.edu/~shade/cs3220.html">Donald's Homepage</div></a></div>
      <div class="footer-flex"><a href="http://judah.cedarville.edu">People's Choice</a></div>
      <div class="footer-flex"><a href="http://judah.cedarville.edu/~towner/cs3220.html">John's Homepage</a></div>
    </footer>
</html>
