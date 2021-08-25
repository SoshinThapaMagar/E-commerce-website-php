<?php
                if (count($errors) > 0) :
 ?>
    <div class= "error" style="background-color: #e4382f; color: white; padding: 0.5rem 1rem; border-radius: 7px; margin-bottom:10px">
        <?php 
        foreach ($errors as $error) : 
        ?>
          <p><?php echo $error ?></p>
        <?php endforeach ?>
    </div>
  <?php  endif  ?>
  <?php
                if (count($successs) > 0) :
 ?>
    <div class= "success">
        <?php 
        foreach ($successs as $success) : 
        ?>
          <p><?php echo $success ?></p>
        <?php endforeach ?>
    </div>
  <?php  endif  ?>