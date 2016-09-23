<?php

      session_start();

      echo '<h1>Integrate</h1>';

      echo  '<a href="/index.php">Home</a><br>';
      echo  '<a href="/login.php">Login</a><br>';
      echo  '<a href="/logout.php">Logout</a><br>';
      echo  '<a href="/signup.php">Signup</a><br>';
      echo  '<a href="/searchschools.php">Search Schools</a><br>';
      echo  '<a href="/searchclasses.php">Search Classes</a><br>';
      echo  '<a href="/classes.php">Classes</a><br>';
      echo  '<a href="/tests.php">Tests</a><br>';
      echo  '<a href="/rate.php">Rate</a><br>';
      echo  '<a href="/addtest.php">Add a Test</a><br>';
      echo  '<a href="/manage.php">Manage</a><br>';

      echo  '<h3>Debug</h3>';
      print_r($_SESSION);

      $status_true = json_encode(Array('status' => true, 'message' => "Logged In"));
      $status_false = json_encode(Array('status' => false, 'message' => "Invalid Email"));
      echo  '<h3>Error Scheme</h3>';
      echo  '<ul><li>True - Successful</li><li>' . $status_true . '</li><li>False - Failed</li><li>' . $status_false . '</li></ul>';

      echo  '<h3>TODO List</h3>';
      echo file_get_contents('TODO.txt');
?>
