<?php


?>


<!doctype html>
<html>
<head>
  <title>
    Open Jobs
  </title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet" />

    <!-- Functions -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script>
    </script>  

</head>
<body>

  <?php
    include("{$_SERVER['DOCUMENT_ROOT']}/jobs-new/includes/header.php")
  ?>

  <!--============= Body content ========================-->

  <div class navbar navbar-default>
    <div class="container">
      <button type="button" class="btn btn-success">
        Create
      </button>
      <button type="button" class="btn btn-success">
        Assign
      </button>
    </div>    
  </div>


  <!--============= End body content ========================-->

  <?php
    include("{$_SERVER['DOCUMENT_ROOT']}/jobs-new/includes/footer.php")
  ?>

</body>
</html>