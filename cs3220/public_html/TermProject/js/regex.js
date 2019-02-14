function validateForm(){
    var email = document.forms["myForm"]["fEmail"].value;
    var message = document.forms["myForm"]["fmessage"].value;
    var element = document.getElementById("error_message");
    element.style.color = "0xff0000";
    if(email.search(/@cedarville\.edu/)>=0){
        if(message != ""){
            return 1;
        }else{
            if(message=""){
                element.innerHTML = "Please Enter a Message...";
                return -1;
            }else{
                element.innerHTML = "";
                return 1;
            }
        }  
    }else{
        element.innerHTML ="Please give a valid Email!";
        element.style.color = "0xff0000";
        return -1;
    }
}
function onEmailChange(){
    var email = document.forms["myForm"]["fEmail"].value;
    var element = document.getElementById("error_message");
    element.style.color = "0xff0000";
    if(email.search(/@cedarville\.edu/)<0){
        element.innerHTML ="Please give a valid Email!";
    }else{
        element.innerHTML = "";
    }
}
function onMessageChange(){
    var message = document.forms["myForm"]["fmessage"].value;
    var element = document.getElementById("error_message");
    element.style.color = "0xff0000";
    if(message==""){
        element.innerHTML = "Please Enter a Message...";
    }else{
        element.innerHTML = "";
    }
}