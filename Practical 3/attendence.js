let rows = document.querySelectorAll("tbody tr");

rows.forEach(function (row) {
    row.addEventListener("click", function () {
        alert("Attendance record selected.");
    });
});