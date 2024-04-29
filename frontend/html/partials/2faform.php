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
<form id="2fa-form">
    <h3>2fa</h3>
    <p> Check your email for the code</p>
    <input type="password" id="code" name="code" placeholder="2fa Code" required>
    <input type="submit" value="Login">
    <img style="width:300px; max-width:100%;" src="https://www.nydailynews.com/wp-content/uploads/migration/2017/01/04/A6XMSMGYQMHJVE3AFFOESKSBC4.jpg">

</form>