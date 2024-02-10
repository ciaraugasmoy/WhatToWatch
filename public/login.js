
function HandleLoginResponse(response)
{
	var text = JSON.parse(response);
	document.getElementById("textResponse").innerHTML = response+"<p>";	
	document.getElementById("textResponse").innerHTML = "response: "+text+"<p>";
}

function SendLoginRequest() {
    console.log("SendLoginRequest function called");
    var username = document.getElementById("username").value;
    var password = document.getElementById("password").value;
    var request = new XMLHttpRequest();
    request.open("POST", "../loginRequest.php", true);
    request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    request.onreadystatechange = function () {
        if ((this.readyState == 4) && (this.status == 200)) {
            HandleLoginResponse(this.responseText);
        }
    };

    // Encoding the parameters? hopefully this weorks
    var params = "type=Login&username=" + encodeURIComponent(username) + "&password=" + encodeURIComponent(password);
    request.send(params);
}
