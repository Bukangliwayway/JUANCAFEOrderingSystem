<?php 
  require 'header.php';
?>

<main>
  <div class="cashier";
    <?php 
      switch ($_GET['error']) {
        case 'emptyfields':
          echo '<p class="error">Empty Fields</p>';
          break;
        case 'invalidcharacters':
          echo '<p class="error">Invalid Characters</p>';
          break;
        case 'wrongpass':
          echo '<p class="error">Wrong Password</p>';
          break;
        case 'sqlerror':
          echo '<p class="error">Sql Error</p>';
          break;
        case 'nouser':
          echo '<p class="error">Invalid Username</p>';
          break;
        default:
          echo '<p></p>';
      }
      //for those who loggedIN
      if(isset($_SESSION['userID'])) echo  '

          <form action="/qt/includes/logout.inc.php" method="post">
            <button type="submit">Logout</button>
          </form>
    ';
      else echo '
          <form action="/qt/includes/login.inc.php" method="post">
            <input type="text" name="username" placeholder="Branch or Username" />
            <input type="password" name="pass" placeholder="Password" />
            <button type="submit" name="login-submit">Login</button>
          </form>
          <a href="signup.php">Sign Up Instead</a>
      ';
    ?>
  </div> 
</main>

<?php
  require "footer.php";
?>
