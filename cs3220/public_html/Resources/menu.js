var selection = "Project1";
function updateOption(){
    var list = document.getElementById("menuList");
    selection = list.options[list.selectedIndex].text;
}
function gotoPage(){
    if(selection != ""){
        var path = 'TermProject/'+selection+".html";
        window.location.pathname = path;
        
    }
}