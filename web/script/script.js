window.onload = function () {
    var renameButton = document.getElementsByClassName("rename_button");

    for (var i = 0; i< renameButton.length; i++ ){
        renameButton[i].onclick = function () {
            this.parentNode.parentNode.childNodes[7].classList.remove("none");
            this.parentNode.parentNode.childNodes[7].className = "form_rename";
            this.parentNode.parentNode.childNodes[7].style.display = 'block';
        }
    }

    var renameFolderButton = document.getElementsByClassName("rename_folder_button");

    for (var i = 0; i< renameFolderButton.length; i++ ){
        renameFolderButton[i].onclick = function () {
            this.parentNode.parentNode.childNodes[7].classList.remove("none");
            this.parentNode.parentNode.childNodes[7].className = "form_rename_folder";
            this.parentNode.parentNode.childNodes[7].style.display = 'block';
        }
    }

}
