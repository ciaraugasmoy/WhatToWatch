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
<form id="login-form">
    <input type="text" id="username" name="username" placeholder="Username" required><br><br>
    <input type="password" id="password" name="password" placeholder="Password" required><br><br>
    <input type="submit" value="Login">
</form>
<p id="error-message" style="color: red;"></p>
