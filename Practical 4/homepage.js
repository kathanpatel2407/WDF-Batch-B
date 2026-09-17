let time = document.createElement("p");
document.body.appendChild(time);

setInterval(function () {
    let date = new Date();
    time.innerHTML = "Current Time: " + date.toLocaleTimeString();
}, 1000);