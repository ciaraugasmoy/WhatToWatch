<style scoped>

section{
  width:max-content;
}
#friends{
   width:max-content;
   border: 2px #0075DE solid;
   border-radius: 10px;
   max-height:300px;
   overflow:scroll;
}
.friend{
  display: flex;
  width:300px;
  padding: 10px 10px;
  justify-content:space-around;
  box-sizing:border-box;
  border: 1px #0075DE20 solid;
}
.friend>*{
  display:inline-block;
}
.friend .options{
  display:flex;
  flex-direction:column;
  gap: 5px;
}
.friend button{
  border: none;
  border-radius:5px;
  width:60px;
  font-family: 'Courier New', monospace;
}
.friend>a{
  color:white;
}
.friend[data-status='pending']{
  background:#0075DE80 
}
form{
  display: flex;
  max-width:100%;
  gap:10px;
  align-content: center;
  margin-bottom:10px;
}
input[type=text],input[type=password]{
  border-radius: 20px;
  border: 2px #0075DE solid;
  padding: 8px;
}
input[type=submit]{
  border-radius: 20px;
  border: 2px #0075DE solid;
  background-color:#0075DE;
  content:'add friend';
  border-radius: 20px;
  justify-self: right;
  padding: 8px;
}
</style>
<h3>Friends</h3>
<section>
 <form>
    <input type="text" id="username" name="username" placeholder="Add friend by Username" required>
   <input type='submit' value='Send Request'>
 </form>
<div id='friends'>
  <div class='friend' data-status='pending'>
    <a href='#'> @friend</a>
    <div class='options'>
    <button>accept</button><button>reject</button>
    </div>
  </div>
    <div class='friend' data-status='pending'>
    <a href='#'> @friend</a>
    <div class='options'>
    <button>cancel</button>
    </div>
  </div>
  <div class='friend'>
    <a href='#'> @friend</a>
    <button>remove</button>
  </div>
   <div class='friend'>
    <a href='#'> @friend</a>
    <button>remove</button>
  </div>
     <div class='friend'>
    <a href='#'> @friend</a>
    <button>remove</button>
  </div>
</div>
</section>