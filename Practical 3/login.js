let form = document.querySelector("form");

form.addEventListener("submit", function (event) 
{

    let username = document.querySelector("input[type='text']").value;
    let password = document.querySelector("input[type='password']").value;

    if (username === "" || password === "") 
        {
        event.preventDefault();
        alert("Please enter username and password.");
    } 
    else 
    {
        alert("Login successful!");
    }

});