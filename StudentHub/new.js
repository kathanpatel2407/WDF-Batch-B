document.addEventListener("DOMContentLoaded", function () {

    // Dashboard
    if (document.title === "Dashboard") {
        alert("Welcome to Student Dashboard!");
    }

    // Attendance
    if (document.title === "Attendance") {
        let rows = document.querySelectorAll("tbody tr");

        rows.forEach(function (row) {
            row.addEventListener("click", function () {
                alert("Attendance record selected.");
            });
        });
    }

    // Courses
    if (document.title === "Registered Courses") {
        let rows = document.querySelectorAll("table tr");

        rows.forEach(function (row) {
            row.addEventListener("click", function () {
                alert("Course selected.");
            });
        });
    }

    // Feedback
    if (document.title === "Feedback") {
        let form = document.querySelector("form");

        form.addEventListener("submit", function (event) {
            event.preventDefault();
            alert("Thank you! Your feedback has been submitted.");
        });
    }

    // Announcements
    if (document.title === "Announcements") {
        let headings = document.querySelectorAll("h3");

        headings.forEach(function (heading) {
            heading.addEventListener("click", function () {
                alert("Announcement selected.");
            });
        });
    }

    // Home Page
    if (document.title === "Home Page") {
        let time = document.createElement("p");
        document.body.appendChild(time);

        setInterval(function () {
            let date = new Date();
            time.innerHTML = "Current Time: " + date.toLocaleTimeString();
        }, 1000);
    }

});