alert("Welcome to CHARUSAT Student Portal");

function changeTheme() {
    document.body.classList.toggle("dark");
}

function menu() {
    document.querySelector("nav").classList.toggle("show");
}

function openModal() {
    document.getElementById("modal").style.display = "block";
}

function closeModal() {
    document.getElementById("modal").style.display = "none";
}

function faq(id) {
    let answer = document.getElementById(id);

    if (answer.style.display === "none") {
        answer.style.display = "block";
    } else {
        answer.style.display = "none";
    }
}

let slide = 0;

function nextSlide() {
    let slides = document.getElementsByClassName("slide");

    for (let i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }

    slide++;

    if (slide >= slides.length) {
        slide = 0;
    }

    slides[slide].style.display = "block";
}