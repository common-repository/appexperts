jQuery(document).ready(function () {

    document.documentElement.style.setProperty('--main-color', colors.main_color);
    document.documentElement.style.setProperty('--secondary-color', colors.secondary_color);
    document.documentElement.style.setProperty('--text-color', colors.text_color);
    document.documentElement.style.setProperty('--primary-btn-text-color', colors.primary_btn_text_color);
    document.getElementById("loader-wrapper").remove();
    document.getElementById("main-content").style.visibility = '';

    let inputs = document.getElementsByTagName('input')
    console.log(inputs);
    for (let i = 0; i < inputs.length; i++) {
        if (inputs[i].value == '') {
            inputs[i].parentElement.classList.add('text-val-empty');
        } else {
            inputs[i].parentElement.classList.remove('text-val-empty');
        }
    }
    var labels = document.getElementsByClassName("wpforms-field");


    for (let i = 0; i < labels.length; i++) {
        labels[i].addEventListener("focusin", addclass);
    }




    for (let i = 0; i < labels.length; i++) {
        labels[i].addEventListener("focusout", removeclass);
    }



});
function removeclass(event) {
    let inputs = document.getElementsByTagName('input')
    console.log(inputs);
    for (let i = 0; i < inputs.length; i++) {
        if (inputs[i].value == '') {
            inputs[i].parentElement.classList.add('text-val-empty');
        } else {
            inputs[i].parentElement.classList.remove('text-val-empty');
        }
    }
    this.classList.remove('transition-text');

}
function addclass(event) {

    this.classList.add('transition-text');

}