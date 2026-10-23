document.addEventListener("DOMContentLoaded", function () {

    const headers = document.querySelectorAll(".notice-header");

    headers.forEach(function (header) {

        header.addEventListener("click", function () {

            const notice = header.parentElement;

            notice.classList.toggle("active");

        });

    });

});