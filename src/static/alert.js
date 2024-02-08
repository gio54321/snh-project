function showAlert(message) {
    document.getElementById('generic_alert').style.visibility = "visible";
    if (message != null) {
        document.getElementById('generic_alert_text').innerText = message;
    }
}

function hideAlert() {
    document.getElementById('generic_alert').style.visibility = "hidden";
}

function hideAlertTimed(millis) {
    setTimeout(hideAlert, millis);
}

hideAlert();