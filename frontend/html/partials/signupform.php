<style scoped>
    form{
        display: grid;
        width:100%;
        padding: 10px;
        background-color: var(--color-theme-accent);
        justify-content: center;
        align-content: center;
        margin: 10px;
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

<p id="error-message" style="color: red;"></p>
<form id="signup-form">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required><br><br>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br><br>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required><br><br>

    <label for="dob">Date of Birth:</label>
    <input type="date" id="dob" name="dob" required><br><br>

    <input type="submit" value="signup">
</form>

<p>Already have an account? <a href="login.php">Login here</a></p>
