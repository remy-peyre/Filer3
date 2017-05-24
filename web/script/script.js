window.onload = function () {
    console.log('coucou');
    var renameButton = document.getElementsByClassName("rename_button");

    for (var i = 0; i< renameButton.length; i++ ){
        renameButton[i].onclick = function () {
            this.parentNode.parentNode.childNodes[11].classList.remove("none");
            this.parentNode.parentNode.childNodes[11].className = "form_rename";
            this.parentNode.parentNode.childNodes[11].style.display = 'block';
        }
    }
}
