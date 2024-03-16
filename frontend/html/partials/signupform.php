<style scoped>
h3{
    margin-bottom:10px;
}
form{
    display: grid;
    max-width:600px;
    width:100%;
    padding: 50px 0 30px 0;
    background-color: var(--color-theme-accent);
    justify-content: center;
    align-content: start;
    margin: 0;
    justify-self: center;
    min-height: 70vh;
    align-self: start;
    gap: 5px;
}
input[type=text],input[type=password],input[type=email],input[type=date]{
    max-width: 500px;
    border-radius: 20px;
    border: 1px blue solid;
    padding: 10px;
    margin: 10px 0;
}
input[type=date]{
    width:150px;
    padding:5px;
}
input[type=submit]{
    width:  100px;
    border:none;
    padding:10px;
    border-radius:5px;
    color: white;
    background:darkred;
    justify-self: center;
    margin:10px 0 20px 0;
}
input[type=submit]:hover{
    background:var(--color-theme-main-tone-down);
}
p a{
    color:red;
}

</style>
<form id="signup-form">
    <h3>Signup</h3>
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" placeholder="username" required><br><br>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" placeholder="password" required><br><br>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" placeholder="example@example.com" required><br><br>

    <label for="dob">Date of Birth:</label>
    <input type="date" id="dob" name="dob" required><br><br>

    <input type="submit" value="signup">
    <p>Already have an account? <a href="login.php">Login here</a></p>

</form>