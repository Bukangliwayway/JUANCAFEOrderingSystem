<?php
  if(!isset($_POST['signup-submit'])){ 
    header("Location: ../signup.php");
    exit();
  }
    require('dbhandler.inc.php');

    $username = $_POST['username'];
    $branch = $_POST['branch'];
    $pass = $_POST['password'];
    $repass = $_POST['password-r'];
    $hashedpass = password_hash($pass, PASSWORD_DEFAULT);

    
    if(empty($username) || empty($branch) || empty($pass)){
      header("Location: ../signup.php?error=emptyfields&username=".$username."&branch=".$branch);
      exit();
    }
    
    if(!preg_match("/^[a-zA-Z0-9]*$/", $username) || !preg_match("/^[a-zA-Z0-9]*$/", $branch)){
      header("Location: ../signup.php?error=invalidcharacters&username=".$username."&branch=".$branch);
      exit();
    }

    if($pass != $repass){
      header("Location: ../signup.php?error=passworddoesntmatch&username=".$username."&branch=".$branch);
      exit();
    }

    $sql = "select username from users where username=?";
    $stmt = mysqli_stmt_init($dbh);

    if(!mysqli_stmt_prepare($stmt, $sql)){
      header("Location: ../signup.php?error=sqlerror");
      exit();
    }
    
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $checkResult = mysqli_stmt_num_rows($stmt);
    
    if($checkResult > 0){
      header("Location: ../signup.php?error=usernamealreadyexists&branch=".$branch);
      exit();
    }
    
    $sql = "insert into users (username, userbranch, userpassword) values (?,?,?)";
    $stmt = mysqli_stmt_init($dbh);
    
    echo 'nani';
    if(!mysqli_stmt_prepare($stmt,$sql)){
      header("Location: ../signup.php?error=sqlerror");
      exit();
    }

    mysqli_stmt_bind_param($stmt, "sss", $username, $branch, $hashedpass);
    mysqli_stmt_execute($stmt);
    header("Location: ../index.php?signup=success");
    exit();

    mysqli_stmt_close($stmt);
    mysqli_close($dbh);
  

