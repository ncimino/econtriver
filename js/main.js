//<a href="javascript:submitForm(this)">Submit</a>
function submitForm(thisfield)
{
	thisfield.form.submit();
}

//OnKeyPress='return enterSubmit(this,event);'
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