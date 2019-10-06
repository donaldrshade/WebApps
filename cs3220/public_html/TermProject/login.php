<?php
   session_start();
   $_SESSION["user_name"] = "";
   if( $_POST["user_name"] && $_POST["password"]) {
      
      $user = $_POST["user_name"];
      $pass = $_POST["password"];
      if(empty($user) or empty($pass)){
         $invalid = true;
         $_POST = array();//clear the data
      }
      else{
         $cred_user = "cs3220";
         $cred_pass = "";
         $db = mysqli_connect("james.cedarville.edu",$cred_user,$cred_pass,"cs3220_Sp19") or die("Error: unable to connect to database");
         
         $db = mysqli_connect("james.cedarville.edu","cs3220","","cs3220_Sp19") or die("Error: unable to connect to database");
         $query = $db->prepare('SELECT name FROM orangeBlob_users WHERE name = ? AND password = ?') or die("BUST");
         $query->bind_param("ss",$user,$pass);
         $query->execute();
         $query->store_result();
         $query->bind_result($user_id);
         $query->fetch();
         $query->close();
         mysqli_close($db);
         if(!empty($user_id)){
            $_SESSION["user_name"] = $user;
            header("Location: Project4.php");
         }
         else{
            $invalid = true;
            $_POST = array();//clear the data
         }
      }
   }
   
?>
<!DOCTYPE html>
<html lang="en">
   <meta charset="utf-8" CONTENT="NO-CACHE">
   <title>Donald Shade - APE</title>
   <link rel="stylesheet" href="Resources/loginCSS.css" type="text/css">
   <body>
   <div class="center">
      <a href="../cs3220.html"> Back to home page</a>
      <a href="http://judah.cedarville.edu/peopleschoice/index.php"> Back to PC </a>
   </div>
   <div class="center">
      <div class = "center login">
         <form action="<?php $_PHP_SELF ?>" method="POST">
            <h1>Ape Login</h1>
            <?php if($invalid){$invalid = false; echo '<div class="error">Invalid Username or Password</div>';}?>
            Username:<br>
            <input type="text" name="user_name"><br>
            Password:<br>
            <input type="password" name="password"><br><br>
            <input type="submit" />
         </form>
      </div>
   </div>
   <div class="center"></div>

   </body>
</html>
