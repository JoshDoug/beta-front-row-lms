var navHeaders = document.getElementsByTagName("nav")[1].getElementsByTagName("h2");

for (var i = 0; i < navHeaders.length; i++) {
    navHeaders[i].addEventListener("click", toggleNav);
}


function toggleNav() {
    this.classList.toggle("hide");
}
