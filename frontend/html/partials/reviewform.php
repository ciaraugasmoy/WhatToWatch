<style scoped>

.review-box{
    margin:10px 0px 20px;
    border-radius: 20px;
    background: linear-gradient(#000000 5px,#000000DD);
    width:500px;
    max-width:100vw; 
    padding: 20px;
    justify-self: center;
    justify-content: center;
    justify-items: center;
    display: grid;
    color:white;
}
form{
  width:500px;
  max-width:100vw;
  padding:10px;
  display:grid;
  justify-content:center;
  color:white;
  gap:5px;
}
form>*{
  justify-self:center;
}
textarea{
  box-sizing:border-box;
  border: 2px solid #0df;
  border-radius:10px;
  height:200px;
  width:500px;
  max-width:98vw;
  resize: none;
  transition:300ms;
}
input[type='submit']{
  margin-top:10px;
  background:#0d8c8c;
  padding: 10px 25px;
  border:none;
  border-radius:50px;
  transition:300ms;
}
input[type='submit']:hover{
  background:#0df;
  transition:300ms;
}
.rating {
  background:#0dffff25;
  border-radius:50px;
  display: inline-block;
  position: relative;
  height: 50px;
  line-height: 40px;
  font-size: 50px;
  padding: 5px 10px;
}

.rating label {
  position: absolute;
  top: 5px;
  left: 10px;
  height: 100%;
  cursor: pointer;
}

.rating label:last-child {
  position: static;
}

.rating label:nth-child(1) {
  z-index: 5;
}

.rating label:nth-child(2) {
  z-index: 4;
}

.rating label:nth-child(3) {
  z-index: 3;
}

.rating label:nth-child(4) {
  z-index: 2;
}

.rating label:nth-child(5) {
  z-index: 1;
}

.rating label input {
  position: absolute;
  top: 0;
  left: 0;
  opacity: 0;
}

.rating label .icon {
  float: left;
  color: transparent;
}

.rating label:last-child .icon {
  color: #000;
}

.rating:not(:hover) label input:checked ~ .icon,
.rating:hover label:hover input ~ .icon {
  color: #0dF;
}

.rating label input:focus:not(:checked) ~ .icon:last-child {
  color: #fff;
  text-shadow: 0 0 5px #09f;
}
</style>
<section class='review-box'>
<h3>Leave a Review</h3>
<form method="post" action="movie_profile.php?id=<?php echo $movie_id; ?>">
<label for='stars'>Rate</label>
<div class="rating">
  <label>
    <input type="radio" name="stars" value="1" />
    <span class="icon">★</span>
  </label>
  <label>
    <input type="radio" name="stars" value="2" />
    <span class="icon">★</span>
    <span class="icon">★</span>
  </label>
  <label>
    <input type="radio" name="stars" value="3" />
    <span class="icon">★</span>
    <span class="icon">★</span>
    <span class="icon">★</span>   
  </label>
  <label>
    <input type="radio" name="stars" value="4" />
    <span class="icon">★</span>
    <span class="icon">★</span>
    <span class="icon">★</span>
    <span class="icon">★</span>
  </label>
  <label>
    <input type="radio" name="stars" value="5" />
    <span class="icon">★</span>
    <span class="icon">★</span>
    <span class="icon">★</span>
    <span class="icon">★</span>
    <span class="icon">★</span>
  </label>
</div>
  <label for='review'>Review</label>
    <textarea name="review"></textarea>
    <input type="submit" value="Submit">
 </form>
</section>