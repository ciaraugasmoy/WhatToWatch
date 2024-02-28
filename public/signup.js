
/*
Password Validation: Assigns values from form to the validation and checks to see if the username is between
3-16 characters, the email is in proper format, the password is over 8 characters, and if the password matches
the confirm password. If any validation is not passed, an "alert" message is displayed with the correction tip.
If validation is passed, the function returns true and the validation accepts it.
*/
function validate(form) {
    let email = form.email.value.trim();
    let username = form.username.value.trim();
    let password = form.password.value.trim();
    let confirm = form.confirm.value;

    if (!/^[a-zA-Z0-9_-]{3,16}$/.test(username)) {
        alert("Invalid username format");
        return false;
    }

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        alert("Invalid email format");
        return false;
    }

    if (password.length < 8) {
        alert("Password must be at least 8 characters long");
        return false;
    }

    if (password !== confirm) {
        alert("Password and Confirm password must match");
        return false;
    }

    return true;
}