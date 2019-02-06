function validateForm(){
    var email = document.forms["myForm"]["fEmail"].value;
    var message = document.forms["myForm"]["fmessage"].value;
    var element = document.getElementById("error_message");
    if(email.search(/@cedarville\.edu/)>=0){
        element.style.color = "0x00ff00";
        if(message != ""){
            return 1;
        }else{
            element.innerHTML = "Please Enter a Message...";
        }
        
    }else{
        
        element.innerHTML ="Please give a valid Email!";
        element.style.color = "0xff0000";
        return -1;
    }
}