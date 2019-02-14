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
        return this.currentSemester;
    }
    getCourses(){
        return this.courses;
    }
    addCourse(newCourse){
        this.courses.push(newCourse);
    }
}
$(document).ready(function(){
    var data = $.getJSON("http://judah.cedarville.edu/~gallaghd/cs3220/termProject/getCombined.php");
    console.log(data);
    $('#logout').click(function() {
        window.location.replace("../cs3220.html");          
    }) 
    
})