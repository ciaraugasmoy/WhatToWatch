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
<form id="signup-form">
    <h3>Signup</h3>
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required><br><br>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br><br>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required><br><br>

    <label for="dob">Date of Birth:</label>
    <input type="date" id="dob" name="dob" required><br><br>

    <input type="submit" value="signup">
    <p>Already have an account? <a href="login.php">Login here</a></p>

</form>