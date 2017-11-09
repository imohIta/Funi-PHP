// set application baseUri here
window.baseUri = '';

// General Functions
function getValue(div){
    return document.getElementById(div).value;
}

function setValue(div, value){
    document.getElementById(div).value = value;
}

function getInnerHtml(div){
    return document.getElementById(div).innerHTML;
}

function setInnerHtml(div, value){
    document.getElementById(div).innerHTML = value;
}

function getSelectValue(div){
    var e = document.getElementById(div);
    return e.options[e.selectedIndex].text;
}

function getElement(div){
    return document.getElementById(div);
}

function disableBtn(btn){
    getElement(btn).setAttribute('disabled', 'disabled');
}

function enableBtn(btn){
    getElement(btn).removeAttribute('disabled');
}

function showDiv(div){
    document.getElementById(div).style.display = 'block';
}

function hideDiv(div){
    document.getElementById(div).style.display = 'none';
}

function showError(msg){

}
