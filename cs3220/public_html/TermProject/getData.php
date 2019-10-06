<?php 
$student = /*orangeBlob*/$_GET["user"];
if(!empty($_GET['plan_name'])){
   $defaultPlan = $_GET['plan_name'];
}
else{
   $defaultPlan = "";
}

//get the user_id from the user name
$db = mysqli_connect("james.cedarville.edu","cs3220","","cs3220_Sp19") or die("Error: unable to connect to database");
$query = $db->prepare('SELECT user_id, currYear, currTerm FROM orangeBlob_users WHERE name = ?');
$query->bind_param("s",$student);
$query->execute();
$query->store_result();
$query->bind_result($user_id,$currYear,$currTerm);
$query->fetch();

$query->close();

//set default plan

//get the plans associated with user
$query = $db->prepare('SELECT * FROM orangeBlob_plans WHERE user_id = ?');
$query->bind_param("i",$user_id);
$query->execute();
$query->store_result();
$query->bind_result($plan_id,$name,$isDefault,$cat_id,$u_id,$major_id);
$plans = array();

while($query->fetch()){
   
   //get catYear
   $cat = $db->prepare('SELECT year FROM orangeBlob_catalogs WHERE id = ?') or die("Why?");
   $cat->bind_param("i",$cat_id);
   $cat->execute();
   $cat->store_result();
   $cat->bind_result($catYear);
   $cat->fetch();
   $cat->close();
   
   //get major
   $maj = $db->prepare('SELECT name FROM orangeBlob_majors WHERE id = ?');
   $maj->bind_param("i",$major_id);
   $maj->execute();
   $maj->store_result();
   $maj->bind_result($major);
   $maj->fetch();
   $maj->close();
   
   $plans[$name] = array();
   $plans[$name]['catYear'] = $catYear;
   $plans[$name]['currTerm'] = $currTerm;
   $plans[$name]['currYear'] = $currYear;
   $plans[$name]['major'] = $major;
   $plans[$name]['name'] = $name;
   $plans[$name]['student'] = $student;
   $plans[$name]['courses'] = array();
   
   $cour = $db->prepare('SELECT course_id, year, term FROM orangeBlob_plan_courses WHERE plan_id = ?') or die("Finding Courses");
   $cour->bind_param("i",$plan_id);
   $cour->execute();
   $cour->store_result();
   $cour->bind_result($courseId,$year,$term);
   
   while($cour->fetch()){
      $co = $db->prepare('SELECT designation FROM orangeBlob_courses WHERE id=?');
      $co->bind_param("i",$courseId);
      $co->execute();
      $co->store_result();
      $co->bind_result($des);
      while($co->fetch()){
         $course = array();
         $course['id'] = $des;
         $course['year'] = $year;
         $course['term'] = $term;
         $plans[$name]['courses'][$des] = $course;

      }
      $co->close();
   }
   $cour->close();
   if(empty($_GET['plan_name']) && $isDefault == true){
      $defaultPlan = $name;
   }
}
$query->close();

$return = array();
$return['plan']=$plans[$defaultPlan];

//Catalog
$catalog = array();
$catalog['year']=$plans[$defaultPlan]['catYear'];
$catalog['courses']=array();

$cat = $db->prepare('SELECT id FROM orangeBlob_catalogs WHERE year = ?');
$cat->bind_param("i",$catalog['year']);
$cat->execute();
$cat->store_result();
$cat->bind_result($cat_id);
$cat->fetch();
$cat->close();

$cata = $db->prepare('SELECT designation, name, description, credit_load FROM orangeBlob_catalog_course as cc left join orangeBlob_courses as c on cc.course_id=c.id  WHERE catalog_id = ?') or die($cata);
$cata->bind_param("i",$cat_id);
$cata->execute();
$cata->store_result();
$cata->bind_result($des,$name,$desc,$load);
while($cata->fetch()){
   $course = array();
   $course['id'] = $des;
   $course['name'] = $name;
   $course['description'] = $desc;
   $course['credits'] = $load;
   
   $catalog['courses'][$des] = $course;
}
$cata->close();
$return['catalog'] = $catalog;


//Requirements
$major = $defaultPlan.major;
$reqs = array();

//get back to major_id
$maj = $db->prepare('SELECT id FROM orangeBlob_majors WHERE name = ?');
$maj->bind_param("s",$major);
$maj->execute();
$maj->store_result();
$maj->bind_result($major_id);
$maj->fetch();
$maj->close();

//We have major_id and cat_id

$requ = $db->prepare('SELECT id FROM orangeBlob_requirements WHERE catalog_id = ? AND EXISTS (SELECT req_id FROM orangeBlob_req_major WHERE major_id = ?)') or die($db->error);
$requ->bind_param("ii",$cat_id,$major_id);
$requ->execute();
$requ->store_result();
$requ->bind_result($req_id);
$requirements = new Requirements();
$requ->fetch();
$requ->close();

$catag = $db->prepare('SELECT id,name FROM orangeBlob_categories WHERE req_id = ?') or die($db->error);
$catag->bind_param("i",$req_id);
$catag->execute();
$catag->store_result();
$catag->bind_result($catag_id,$name);
while($catag->fetch()){
   $requirements->addCategory($name);
   $cat_course = $db->prepare('SELECT course_id FROM orangeBlob_category_course WHERE category_id = ?')or die($db->error);
   $cat_course->bind_param("i",$catag_id);
   $cat_course->execute();
   $cat_course->store_result();
   $cat_course->bind_result($c_id);
   while($cat_course->fetch()){
      $getCourse = $db->prepare('SELECT designation FROM orangeBlob_courses WHERE id = ?')or die($db->error);
      $getCourse->bind_param("i",$c_id);
      $getCourse->execute();
      $getCourse->store_result();
      $getCourse->bind_result($des);
      $getCourse->fetch();
      $requirements->categories[$name]->addCourse($des);
      $getCourse->close();
   }
   $cat_course->close();
}
$catag->close();
$return['plan_names'] = array();
foreach($plans as &$p){
   $return['plan_names'][] = $p['name'];
}
$return['student']= $student;
$return['requirements'] = $requirements;

//Return statement DO NOT REMOVE
echo json_encode($return);

$db->close();

class Requirements {
        public $categories;
        public function __construct() {
            $this->categories = array();
        }
        public function addCategory($cat){
            $this->categories[$cat] = new Category();
        }
}

class Category {
        public $courses;
        public function __construct() {
            $this->courses = array();
        }
        public function addCourse($course){
            $this->courses[] = $course;
        }
}

?>
