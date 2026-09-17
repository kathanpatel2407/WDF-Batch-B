
document.getElementById("registrationForm").addEventListener("submit", function(event) {

    event.preventDefault();

    let name = document.getElementById("name").value;
    let email = document.getElementById("email").value;
    let mobile = document.getElementById("mobile").value;
    let password = document.getElementById("password").value;
    let confirmPassword = document.getElementById("confirmPassword").value;
    let course = document.getElementById("course").value;
    let year = document.getElementById("year").value;
    let gender = document.querySelector('input[name="gender"]:checked');
    let terms = document.getElementById("terms").checked;

    let nameRegex = /^[A-Za-z ]+$/;
    let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    let mobileRegex = /^[0-9]{10}$/;
    let passwordRegex = /^(?=.*[A-Za-z])(?=.*[0-9]).{6,}$/;

    if (!nameRegex.test(name)) {
        alert("Enter a valid name");
        return;
    }

    if (!emailRegex.test(email)) {
        alert("Enter a valid email");
        return;
    }

    if (!mobileRegex.test(mobile)) {
        alert("Enter a valid 10 digit mobile number");
        return;
    }

    if (!passwordRegex.test(password)) {
        alert("Password must contain letters, numbers and at least 6 characters");
        return;
    }

    if (password !== confirmPassword) {
        alert("Passwords do not match");
        return;
    }

    if (course === "") {
        alert("Please select a course");
        return;
    }

    if (year === "") {
        alert("Please select a year");
        return;
    }

    if (!gender) {
        alert("Please select gender");
        return;
    }

    if (!terms) {
        alert("Please accept the terms and conditions");
        return;
    }

    document.getElementById("message").innerHTML =
        "Registration Successful!";

});