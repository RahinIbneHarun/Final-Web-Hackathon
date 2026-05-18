function checkRegisterName() {
  let name = document.getElementById("register_name").value;
  xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById("registerNameResponse").innerHTML = this.responseText;
    }
  };
  xhttp.open("POST", "../Controller/HandleAjax.php", true);
  xhttp.setRequestHeader("content-type", "application/x-www-form-urlencoded");
  xhttp.send("type=register_name&value=" + encodeURIComponent(name));
}

function checkRegisterEmail() {
  let email = document.getElementById("register_email").value;
  xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById("registerEmailResponse").innerHTML = this.responseText;
    }
  };
  xhttp.open("POST", "../Controller/HandleAjax.php", true);
  xhttp.setRequestHeader("content-type", "application/x-www-form-urlencoded");
  xhttp.send("type=register_email&value=" + encodeURIComponent(email));
}
