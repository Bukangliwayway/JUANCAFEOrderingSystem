<?php 
  require 'header.php';
?>

<main>
  <div class="wrapper-main">
    <h1>Sign Up</h1>
    <form action="/qt/includes/signup.inc.php" method="post">
      <input type="text" name="branch" placeholder="Input Your Branch">
      <input type="text" name="username" placeholder="Input Your Username">
      <input type="password" name="password" placeholder="Input your Password">
      <input type="password" name="password-r" placeholder="Reinput your Passord">
      <button type="submit" name="signup-submit">Sign Up</button>
    </form>
    <a href="cashier.php">Sign In</a>
    <?php 
      switch ($_GET['error']) {
        case 'emptyfields':
          echo '<p class="error">Empty Fields</p>';
          break;
        case 'invalidcharacters':
          echo '<p class="error">Invalid Characters</p>';
          break;
        case 'passworddoesntmatch':
          echo '<p class="error">Passwords Does not Match</p>';
          break;
        case 'sqlerror':
          echo '<p class="error">Sql Error</p>';
          break;
        case 'usernamealreadyexists':
          echo '<p class="error">Username Already Exists</p>';
          break;
        default:
          echo '<p></p>';
      }
    ?>
  </div>
</main>

<?php
  require "footer.php";
?>
