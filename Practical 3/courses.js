let rows = document.querySelectorAll("table tr");

rows.forEach(function (row) {
    row.addEventListener("click", function () {
        alert("Course selected.");
    });
});