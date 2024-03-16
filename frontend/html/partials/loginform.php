<style scoped>
h3{
    margin-bottom: 10px;
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
input[type=text],input[type=password]{
    max-width: 500px;
    border-radius: 20px;
    border: 1px blue solid;
    padding: 10px;
    margin: 10px 0;
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
<form id="login-form">
    <h3>Login</h3>
    <input type="text" id="username" name="username" placeholder="Username" required>
    <input type="password" id="password" name="password" placeholder="Password" required>
    <input type="submit" value="Login">
    <p>Don't have an account? <a href="signup.php">Sign Up Here</a></p>
</form>