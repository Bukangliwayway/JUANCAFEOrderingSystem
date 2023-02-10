<?php
  if(!isset($_POST['login-submit'])){ 
    header("Location: ../login.php");
    exit();
  }
    require 'dbhandler.inc.php';  
    $username = $_POST['username'];
    $pass = $_POST['pass'];
    
    //Screens Empty Fields
    if(empty($username) || empty($pass)){
      header("Location: ../cashier.php?error=emptyfields");
      exit();
    }

    if(!preg_match("/^[a-zA-Z0-9]*$/", $username) || !preg_match("/^[a-zA-Z0-9]*$/", $branch)){
      header("Location: ../cashier.php?error=invalidcharacters&username=".$username."&branch=".$branch);
      exit();
    }
    
    $query = "select * from users where username=? or userbranch=?;";
    $stmt = mysqli_stmt_init($dbh);
    
    //Screens Error Statement on SQL
    if(!mysqli_stmt_prepare($stmt,$query)){
      header("Location: ../cashier.php?error=sqlerror");
      exit();
    }
    mysqli_stmt_bind_param($stmt, "ss", $username, $userbranch);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if($row = mysqli_fetch_assoc($result)){
      
      //Screens Wrong Pass
      if(!password_verify($pass, $row['userpassword'])){
        header("Location: ../cashier.php?error=wrongpass");
        exit();
      } 
      
      session_start();
      $_SESSION['username'] = $row['username'];
      $_SESSION['userID'] = $row['userID'];
      
      header("Location: ../cashier.php?login=success");
      exit();
    }
    header("Location: ../cashier.php?error=nouser");
    exit();

  
