<?php
session_start();
$user = $_SESSION["user_name"];
if(!isset($_SESSION["user_name"])){
   $loginURL = "login.php";
   header('Location: '.$loginURL);
}
if(empty($_SESSION["user_name"])){
   $loginURL = "Location: login.php";
   header($loginURL);
}
function debugToConsole($msg) { 
        echo "<script>console.log(".json_encode($msg).")</script>";
}
function clearSession(){
   $_SESSION["user_name"] = "";
}
?>
<!DOCTYPE html>
<html lang="en" >
    <meta charset="utf-8" CONTENT="NO-CACHE">
    <title>Donald Shade - APE</title>
    <link rel="stylesheet" href="Resources/apeCss.css" type="text/css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" type="text/css">
    <script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="http://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="js/apeScript4.js"></script>
    <body>
       <div class = "hidden" id = "user">
       <?php echo $user; ?>
       </div>
        <div class = "header">
            <div id="header-left"><h1>APE 4 (x95)</h1></div>
            <div id="header-right">
                <div id = "student-info">
                    <div class= "info" id="info-name"></div>
                    <div class= "info" id="info-major"></div>
                    <div class= "info" id="info-plan-name">
                      <select id = "info-plans"></select>
                    </div>
                    <div class= "info" id="info-cat"></div>
                    <div class= "info" id="info-curr-sem"></div>
                </div>
                <div id = "options">
                    <div class = "option-button">Options</div>
                    <div class = "option-button" id = "peoples-choice">Course Website</div>
                    <div class = "option-button" id = "logout">Log Out</div>
                </div>
            </div> 
        </div>
        <div class = "content">
            <div id = "ape-left">
                <div id = "ape-upper-left">
                    <h2>Plan Requirements</h2>
                    <div id = "req-accordion">
                    </div>
                </div>
                <div id = "ape-lower-left">
                </div>
            </div>
            <div id = "ape-right">
                <div id = "ape-upper-right">
                    <div class = "ape-plan-header-encapsulate">
                     <div class = "ape-plan-header-credits"></div>
                     <div class = "ape-plan-header" id = "plan_name">Academic Plan</div>
                     <div class = "ape-plan-header-credits" id="credits">Credits:  hours</div>
                    </div>
                    <div id = "ape-plan-content"></div>
                </div>
                <div id = "ape-lower-right">
                  <div class = "ape-header" id = "cat_year">Catalog</div>
                  <div class = "scrolltable" id = "myTable">
                     <table id = "catalog-table">
                        <thead>
                           <tr>
                              <th class = "id">Course ID</th>
                              <th class = "catalog-table-name">Title</th>
                              <th class = "desc">Description</th>
                              <th class = "credits">Credits</th>
                           </tr>
                        </thead>
                     </table>
                  </div>
                </div>
            </div>
        </div>
    </body>
</html>
