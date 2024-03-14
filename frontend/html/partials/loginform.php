<style scoped>
    form{
        display: grid;
        max-width:500px;
        padding: 10px;
        background-color: var(--color-theme-accent);
        justify-content: center;
        align-content: center;
        margin: 10px;
        justify-self: center;
    }
    input[type=text],input[type=password]{
        max-width: 500px;
        border-radius: 20px;
        border: 1px blue solid;
        padding: 10px;
        margin: 10px;
    }
    input[type=submit]{
        width: 60px;
        border-radius: 50%;
        justify-self: right;
    }
</style>
<form id="login-form">
    <h3>Login</h3>
    <input type="text" id="username" name="username" placeholder="Username" required>
    <input type="password" id="password" name="password" placeholder="Password" required>
    <input type="submit" value="Login">
    <p>Don't have an account? <a href="signup.php">Sign Up Here</a></p>
</form>