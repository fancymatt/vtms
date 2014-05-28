function prepareEventHandlers() {
	document.getElementById("new_series_form").onsubmit = function() {
		if (document.getElementById("series_name").value == "") {
			document.getElementById("errorMessage").innerHTML = "Please provide an email address";
			return false;
		} else {
			document.getElementById("errorMessage").innerHTML = "";
			return true;
		}
	};
}

window.onload = function() {
	prepareEventHandlers();
}