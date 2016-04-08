//Check valid Knumber
var form = document.getElementsByTagName("form")[0];
form.addEventListener("submit", validateInput, false);

function validateInput() {
    var kNumb = document.getElementById("kNumber").value;
    var kCheck = kNumb.charAt(0).toLowerCase();
    if (kCheck != 'k') {
        event.preventDefault();
        alert("Username must start with a K");
    }
}