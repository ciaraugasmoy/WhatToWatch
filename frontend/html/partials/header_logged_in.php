<style scoped>
#header{
    max-height: min-content;
    padding: var(--padding-min);
    display: grid;
    color: white;
    background-color: var(--color-theme-main);
    align-items: center;
    justify-items: center;
    grid-template-columns: 8fr 2fr;
}
#header h1{
    font-family: var(--font-family-logo);
    font-size: 1rem;
}
#header img{
    height: 50px;
    width: 50px;
    align-self: center;
    justify-self: center;
}

#menu-button {
    position:absolute;
    top: 10px;
    right:10px;
    z-index:4;
    width: 30px;
    height: 30px;
    display: grid;
    align-content: space-between;
    padding: 2px;
}

#menu-button div {
    width: 100%;
    height: 4px;
    background-color: #fff;
    transition: 0.3s;
}

#nav {
    position: absolute;
    display:grid;
    max-width: 100%;
    height: 300px;
    width: 300px;
    justify-self:end;
    z-index: 1;
    transition:400ms;
}

#nav a {
    padding: 20px;
    text-decoration: none;
    color: #fff;
    background-color: var(--color-theme-main-tone-down);
    font-size: 18px;
}
#nav a:last-child{
  border-radius: 0 0 10px 10px;
}

#menu-button.active div:nth-child(1) {
    transform: rotate(-45deg) translate(-8px, 8px);
}

#menu-button.active div:nth-child(2) {
    opacity: 0;
}

#menu-button.active div:nth-child(3) {
    transform: rotate(45deg) translate(-8px, -8px);
}

.none{
  transition:400ms;
  margin-right:-400px;
}
#logo{
    display: flex;
    justify-content: center;
    flex-direction: column;
}
</style>

<header id="header">
    <div id='logo'>
        <img src='../images/logo.png'>
        <h1>What2Watch</h1>
    </div>
     <div id="menu-button" onclick="toggle()">
      <div></div>
      <div></div>
      <div></div>
    </div>
</header>
<nav id="nav" class="none">
  <a href="#">Home</a>
  <a href="#">Discover</a>
  <a href="#">Profile</a>
  <a href="#">Contact</a>
</nav>
