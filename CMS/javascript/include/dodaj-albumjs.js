function albumnamecheck()
{
    var regex = new RegExp(/^[\\a-zA-Z0-9.!;#$%@&'*+=?^/_`{|}~-\s]{1,99}$/);
    var regex2 = new RegExp(/[^\s]+/);
    if(regex.test(document.getElementById("albumname").value)&& regex2.test(document.getElementById("albumname").value))
    {
        document.getElementById("albumname").style.border ="1px solid green";
        document.getElementById("albumname").style.boxShadow ="0 0 1px green";
        document.getElementById("albummistake").style.display="none";
}		
else {
        document.getElementById("albumname").style.border ="1px solid red";
        document.getElementById("albumname").style.boxShadow ="0 0 3px #8B0000";
        document.getElementById("albummistake").innerHTML="(Nazwa albumu nie powinna być pusta i nie przekraczać 100 znaków)";
        $("#albummistake").fadeIn();
    }

}