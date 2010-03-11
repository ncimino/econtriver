//<a href="javascript:submitForm(this)">Submit</a>
function submitForm(thisfield)
{
	document.getElementById(thisfield).submit();
}

//onkeypress='return enterSubmit(this,event);'
function enterSubmit(thisfield,thisevent)
{
    var keycode;
    if (window.event) keycode = window.event.keyCode;
    else if (thisevent) keycode = thisevent.which;
    else return true;

    if (keycode == 13)
    {
        thisfield.form.submit();
        return false;
    }
    else
        return true;
}

//onfocus='clearField(this,"Some Value")'
function clearField(obj,initialvalue)
{
if (obj.value==initialvalue)
	{
	obj.value="";
	}
}

//onclick='return confirmSubmit("Are you sure?")'
function confirmSubmit(msg)
{
var agree=confirm(msg);
if (agree)
	return true ;
else
	return false ;
}