<div class="reply">
  <h4><?php echo $data['user_id']; ?></h4>
  <p><?php echo $data['message']; ?></p>
  <?php $parent_message_id = $data['message_id']; ?>
  <button class="reply" onclick = "reply(<?php echo $parent_message_id; ?>, '<?php echo $data['user_id']; ?>');">Reply</button>
  <?php
  unset($datas);
  $datas = mysqli_query($conn, "SELECT * FROM discussion_posts WHERE parent_message_id = $parent_message_id");
  if(mysqli_num_rows($datas) > 0) {
    foreach($datas as $data){
      require 'reply.php';
    }
  }
  ?>
</div>
