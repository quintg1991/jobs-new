<?php

  /***                                          ***\

    File name: config.ini
    Author: Quint GLover

    Copyright (c) 2017 University of North Alabama

  \***                                          ***/

  //default settings
  ini_set('magic_quotes_gpc', 'Off');
  date_default_timezone_set('America/Chicago');
  ini_set('display_errors', 'On');
  
  //read .ini file into array
  $_ini = parse_ini_file("{$_SERVER['DOCUMENT_ROOT']}/jobs-new/etc/config.ini", true);

  //When connecting to the server, set names to utf8 encoding
  $_opt = array
  (
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"
  ); 

  $_dsn = "{$_ini['database']['dialect']}:host=127.0.0.1;dbname=" . strtolower($_ini['database']['schema']);
  
  $_pdo = new PDO($_dsn, 'root', '', $_opt);

  $_username = 'Una';
  $_password = sha1('Una' . 'lions');

  $_sql = "INSERT INTO `users` (`username`, `password`, `initials`, `role`, `added`) VALUES (:username, :password, :initials, :role, :added)";
  $_stmnt = $_pdo->prepare($_sql);

  $_params =
    array
    (
      ':username' => $_username,
      ':password' => $_password,
      ':initials' => 'TT',
      ':role' => 1,
      ':added' => date("Y-m-d H:i:s")
    );

  if($_stmnt->execute($_params))
  {
    echo $_pdo->lastInsertId();
  }
  else
  {
    echo $_stmnt->errorInfo();
  }
  