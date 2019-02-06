class course {
    constructor(designator,title,term,year){
        this.designator = designator;
        this.title = title;
        this.term = term;
        this.year = year;
    }
    getName(){
        return this.designator+" "+this.title;
    }
    getDesignator(){
        return this.designator;
    }
    getTitle(){
        return this.title;
    }
    getTerm(){
        return this.term;
    }
    getYear(){
        return this.year;
    }
}
class year{
    constructor(year){
        this.year = year;
        this.fallSem = [];
        this.springSem = [];
        this.summerSem = [];
    }
    addCourse(course){
        if(course.getTerm() == "FA"){
            this.fallSem.push(course);
        }else if(course.getTerm() == "SP"){
            this.springSem.push(course);
        }else{
            this.summerSem.push(course);
        }
    }
    getFallCourses(){
        return this.fallSem;
    }
    getSpringCourses(){
        return this.springSem;
    }
    getSummerCourses(){
        return this.summerSem;
    }
    getData(){
        var rawHtml = "";
        rawHtml += "<div class = \"semester\">";
        rawHtml += "<div class = \"ape-semester-header\">";
        rawHtml += "FA" + this.year;
        rawHtml += "</div>";
        var i;
        for(i = 0;i<this.fallSem.length;i++){
            rawHtml += "<div class= \"ape-class\">";
            rawHtml += this.fallSem[i].getName();
            rawHtml += "</div>";
        }
        rawHtml += "</div>";
        
        rawHtml += "<div class = \"semester\">";
        rawHtml += "<div class = \"ape-semester-header\">";
        rawHtml += "SP" + (this.year+1);
        rawHtml += "</div>";
        for(i = 0;i<this.springSem.length;i++){
            rawHtml += "<div class= \"ape-class\">";
            rawHtml += this.springSem[i].getName();
            rawHtml += "</div>";
        }
        rawHtml += "</div>";
        
        rawHtml += "<div class = \"semester\">";
        rawHtml += "<div class = \"ape-semester-header\">";
        rawHtml += "SU" + (this.year+1);
        rawHtml += "</div>";
        for(i = 0;i<this.summerSem.length;i++){
            rawHtml += "<div class= \"ape-class\">";
            rawHtml += this.summerSem[i].getName();
            rawHtml += "</div>";
        }
        rawHtml += "</div>";
        
        return rawHtml;
    }
}
class schedule{
    constructor(courses){
        var smallestYear = parseInt(courses[0].getYear(),10);
        var largestYear = parseInt(courses[0].getYear(),10);
        var i;
        for(i = 1;i<courses.length;i++){
            if(parseInt(courses[i].getYear(),10) < smallestYear){
                smallestYear = parseInt(courses[i].getYear(),10);
            }
            if(parseInt(courses[i].getYear(),10) > largestYear){
                largestYear = parseInt(courses[i].getYear(),10);
            }  
        }
        this.years = [];
        
        for(i = 0;i<largestYear-smallestYear;i++){
            this.years.push(new year(smallestYear+i));
        }
        for(i = 0;i<courses.length;i++){
            if(courses[i].getTerm() == "FA"){
                var a = parseInt(smallestYear);
                var b = parseInt(courses[i].getYear());
                
                this.years[b-a].addCourse(courses[i]);
            }
            else{
                var a = parseInt(smallestYear,10)+1;
                var b = parseInt(courses[i].getYear(),10);
                this.years[b-a].addCourse(courses[i]);
            }
        }
    }
    getData(){
        var rawHtml = "";
        var i;
        for(i = 0;i<this.years.length;i++){
            rawHtml += "<div class = \"year\">";
            rawHtml+=this.years[i].getData();
            rawHtml += "</div>";
        }
        return rawHtml;
    }
}
class plan {
    constructor(studentName,major,planName,catalogYear,currentSemester){
        this.studentName = studentName;
        this.major = major;
        this.planName = planName;
        this.catalogYear = catalogYear;
        this.currentSemester = currentSemester;
        this.courses = [];
    }
    getStudentName(){
        return this.studentName;
    }
    getMajor(){
        return this.major;
    }
    getPlan(){
        return this.planName;
    }
    getCatalogYear(){
        return this.catalogYear;
    }
    getCurrentSemester(){
        return this.currentSemester();
    }
    getCourses(){
        return this.courses;
    }
    addCourse(newCourse){
        this.courses.push(newCourse);
    }
}
var classes = new Array(44);
classes[0] = new course("EGGN-1010","The Engineering Profes","FA","2016");
classes[1] = new course("CHEM-1050","Chem for Engineers","FA","2016");
classes[2] = new course("CS-1220","C++ Programming","FA","2016");
classes[3] = new course("COMM-1100","Fundementals of Speech","FA","2016");
classes[4] = new course("MATH-1710","Calculus I","FA","2016");
classes[5] = new course("EGCP-1010","Digital Logic Design","FA","2016");

classes[6] = new course("MATH-1720","Calculus II","SP","2017");
classes[7] = new course("PHYS-2110","General Physics I","SP","2017");
classes[8] = new course("EGME-1810","Engineering Graphics","SP","2017");
classes[9] = new course("BTGE-1720","Spiritual Formation","SP","2017");
classes[10] = new course("CS-1220","Obj-Orient Design/C++","SP","2017");

classes[11] = new course("CS-2210","Data Struct Using Java","FA","2017");
classes[12] = new course("EGME-2570","Statics and Dynamics","FA","2017");
classes[13] = new course("MATH-2740","Differential Equations","FA","2017");
classes[14] = new course("PHYS-2120","General Physics II","FA","2017");
classes[15] = new course("ENG-1400","Composition","FA","2017");
classes[16] = new course("PEF-1990","Phys Act & The Christi","FA","2017");

classes[17] = new course("BTGE-2730","Old Testament Literatu","SP","2018");
classes[18] = new course("CS-3310","Operating Systems","SP","2018");
classes[19] = new course("EGCP-3210","Computer Architecture","SP","2018");
classes[20] = new course("MATH-2520","Discreet Math/Prob Pri","SP","2018");
classes[21] = new course("EGEE-2010","Circuits","SP","2018");

classes[22] = new course("BTGE-2740","New Testament Literatu","SU","2018");
classes[23] = new course("HUM-1400","Intro to Humanities","SU","2018");

classes[24] = new course("CS-3410","Algorithms","FA","2018");
classes[25] = new course("EGCP-2120","Microcontrollers","FA","2018");
classes[26] = new course("EGCP-4310","Computer Networks","FA","2018");
classes[27] = new course("PYCH-1400","General Psychology","FA","2018");
classes[28] = new course("HIST-1110","US History I","FA","2018");

classes[29] = new course("BTGE-3755","Theology I","SP","2019");
classes[30] = new course("CS-3220","Web Applications","SP","2019");
classes[31] = new course("CS-3350","Foundations Computer S","SP","2019");
classes[32] = new course("CS-3610","Database Org & Design","SP","2019");
classes[33] = new course("EGCP-3010","Adv Digital Logic Desi","SP","2019");

classes[34] = new course("CS-4810","Software Engineering I","FA","2019");
classes[35] = new course("EGGN-4010","Senior Seminar","FA","2019");
classes[36] = new course("EGCP-4210","Advanced Computer Architecture","FA","2019");
classes[37] = new course("GSS-1100","Politics and America Culture","FA","2019");
classes[38] = new course("LIT-2340","Western Literature","FA","2019");

classes[39] = new course("CS-3510","Compiler Theory and Practice","SP","2020");
classes[40] = new course("CS-4820","Software Engineering II","SP","2020");
classes[41] = new course("EGGN-3110","Professional Ethics","SP","2020");
classes[42] = new course("BTGE-3765","Theology II","SP","2020");
classes[43] = new course("GBIO-1000","Principles of Biology","SP","2020");

var myPlan = new plan("Donald Shade","Computer Science","CPE_Lite","2017","SP2019");
var i;
for(i = 0;i<classes.length;i++){
    myPlan.addCourse(classes[i]);
}

function loadData(){
    var ape = document.getElementById("ape-plan-content");
    var myCourses = new schedule(myPlan.getCourses());
    ape.innerHTML = myCourses.getData();
}
