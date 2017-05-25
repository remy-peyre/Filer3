window.onload = function () {
    var renameFolderButton = document.getElementsByClassName("rename_folder_button");

    for (var i = 0; i< renameFolderButton.length; i++ ){
        renameFolderButton[i].onclick = function () {
            this.parentNode.parentNode.childNodes[7].classList.remove("none");
            this.parentNode.parentNode.childNodes[7].className = "form_rename_folder";
            this.parentNode.parentNode.childNodes[7].style.display = 'block';
        }
    }
}