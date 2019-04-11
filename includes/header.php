<!-- Header -->
<nav class="navbar navbar-default">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">
        UCJobsystem
      </a>
    </div>
    <div class="">
      <ul class="nav navbar-nav navbar-right">
        <?php
          if($_SESSION['role'] == "admin")
          {
            echo '<li>';
              echo '<a href="userManager.php">';
                echo 'User Manager';
                echo '<span class="sr-only">(current)</span>';
              echo '</a>';
            echo '</li>';
          }
        ?>
        <li>
          <a href="tasks.php">
            Tasks
            <span class="sr-only">
              (current)
            </span>
          </a>
        </li>
        <?php
          if($_SESSION['role'] == "admin")
          {
            echo '<li>';
              echo '<a href="reports.php">';
                echo 'Reports';
                echo '<span class="sr-only">(current)</span>';
              echo '</a>';
            echo '</li>';
          }
        ?>
        <li>
          <a href="login.php">
            Logout
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
