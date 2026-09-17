let headings = document.querySelectorAll("h3");

headings.forEach(function (heading) {
    heading.addEventListener("click", function () {
        alert("Announcement selected.");
    });
});