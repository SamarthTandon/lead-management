<?php
session_start();
date_default_timezone_set("Asia/Kolkata");
require 'connection.php';
if (isset($_SESSION['username']) && isset($_SESSION['password'])) {
  $username = $_SESSION['username'];
  $password = $_SESSION['password'];
  $sql = "SELECT * FROM `users` WHERE `username` = '$username' AND `password` = '$password' AND `access` != 'admin'";
  $result = $conn->query($sql);
  if ($result->num_rows == 0) {
    session_destroy();
    header('Location: index.php');
    die();
  } else {
    $current_user = $result->fetch_assoc();
  }
} else {
  session_destroy();
  header('Location: index.php');
  die();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Dashboard | Prospect Management System</title>

  <!-- Custom fonts for this template-->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

  <!-- Page level plugin CSS-->
  <link href="vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="css/sb-admin.css" rel="stylesheet">

</head>

<body id="page-top">

  <nav class="navbar navbar-expand navbar-dark bg-dark static-top">

    <a class="navbar-brand mr-1" href="home.php">Prospect System</a>

    <button class="btn btn-link btn-sm text-white order-1 order-sm-0" id="sidebarToggle" href="#">
      <i class="fas fa-bars"></i>
    </button>

    <!-- Navbar Search -->
    <form class="d-none d-md-inline-block form-inline ml-auto mr-0 mr-md-3 my-2 my-md-0" style="visibility: hidden;">
      <div class="input-group">
        <input type="text" class="form-control" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
        <div class="input-group-append">
          <button class="btn btn-primary" type="button">
            <i class="fas fa-search"></i>
          </button>
        </div>
      </div>
    </form>

    <!-- Navbar -->
    <ul class="navbar-nav ml-auto ml-md-0">
      <li class="nav-item dropdown no-arrow">
        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fas fa-user-circle fa-fw"></i><span></span>
        </a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
          <span class="dropdown-item" style="pointer-events: none;"><?php echo $current_user['name']; ?></span>
          <span class="dropdown-item" style="pointer-events: none; font-size: 12px;"><?php echo $current_user['email']; ?></span>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="change-password.php">Change Password</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">Logout</a>
        </div>
      </li>
    </ul>

  </nav>

  <div id="wrapper">

    <!-- Sidebar -->
    <ul class="sidebar navbar-nav">
      <li class="nav-item active">
        <a class="nav-link" href="home.php">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Dashboard</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="schedule.php">
          <i class="fas fa-fw fa-user-tie"></i>
          <span>Scheduled Follow-ups</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="add-prospect.php">
          <i class="fas fa-fw fa-user"></i>
          <span>Add Prospect</span>
        </a>
      </li>
    </ul>

    <div id="content-wrapper">

      <div class="container-fluid">

        <!-- Breadcrumbs-->
        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="#">Dashboard</a>
          </li>
          <li class="breadcrumb-item active">Overview</li>
        </ol>
        <?php
        $uid = $current_user['uid'];

        $sql = "SELECT * FROM `prospects` WHERE `uid` = '$uid'";
        $result = $conn->query($sql);
        $prospects = array();
        $total = $converted = $open = $closed = 0;
        while ($row = $result->fetch_assoc()) {
          $total++;
          if ($row['status'] == "converted") {
            $converted++;
          }
          if ($row['status'] == "open") {
            $open++;
          }
          if ($row['status'] == "closed") {
            $closed++;
          }
          array_push($prospects, $row);
        }
        ?>
        <!-- Icon Cards-->
        <div class="row">
          <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card text-white bg-primary o-hidden h-100">
              <div class="card-body">
                <div class="card-body-icon">
                  <i class="fas fa-fw fa-user-tie"></i>
                </div>
                <div class="mr-5"><?php echo $total; ?> My Prospects</div>
              </div>
              <a class="card-footer text-white clearfix small z-1" href="view-users.php?u=all">
                <span class="float-left">View All</span>
                <span class="float-right">
                  <i class="fas fa-angle-right"></i>
                </span>
              </a>
            </div>
          </div>
          <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card text-white bg-warning o-hidden h-100">
              <div class="card-body">
                <div class="card-body-icon">
                  <i class="fas fa-fw fa-users"></i>
                </div>
                <div class="mr-5"><?php echo $open; ?> Open Prospects</div>
              </div>
              <a class="card-footer text-white clearfix small z-1" href="view-users.php?u=open">
                <span class="float-left">View All</span>
                <span class="float-right">
                  <i class="fas fa-angle-right"></i>
                </span>
              </a>
            </div>
          </div>
          <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card text-white bg-success o-hidden h-100">
              <div class="card-body">
                <div class="card-body-icon">
                  <i class="fas fa-fw fa-check-circle"></i>
                </div>
                <div class="mr-5"><?php echo $converted; ?> Conversions</div>
              </div>
              <a class="card-footer text-white clearfix small z-1" href="view-users.php?u=converted">
                <span class="float-left">View All</span>
                <span class="float-right">
                  <i class="fas fa-angle-right"></i>
                </span>
              </a>
            </div>
          </div>
          <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card text-white bg-danger o-hidden h-100">
              <div class="card-body">
                <div class="card-body-icon">
                  <i class="fas fa-fw fa-user-slash"></i>
                </div>
                <div class="mr-5"><?php echo $closed; ?> Closed Prospects</div>
              </div>
              <a class="card-footer text-white clearfix small z-1" href="view-users.php?u=closed">
                <span class="float-left">View All</span>
                <span class="float-right">
                  <i class="fas fa-angle-right"></i>
                </span>
              </a>
            </div>
          </div>
        </div>

        <!-- DataTables Example -->
        <div class="card mb-3">
          <div class="card-header">
            <i class="fas fa-users"></i>
            My Prospects</div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered text-center" id="dataTable" width="100%" cellspacing="0">
                <thead>
                  <tr>
                    <th style="vertical-align: middle;">Name</th>
                    <th style="vertical-align: middle;">Mobile No.</th>
                    <th style="vertical-align: middle;">Email</th>
                    <th style="vertical-align: middle;">Status</th>
                    <th style="vertical-align: middle;">Actions</th>
                  </tr>
                </thead>
                <tfoot>
                  <tr>
                    <th style="vertical-align: middle;">Name</th>
                    <th style="vertical-align: middle;">Mobile No.</th>
                    <th style="vertical-align: middle;">Email</th>
                    <th style="vertical-align: middle;">Status</th>
                    <th style="vertical-align: middle;">Actions</th>
                  </tr>
                </tfoot>
                <tbody>
                  <?php
                  foreach ($prospects as $prospect) {
                  ?>
                    <tr>
                      <td style="vertical-align: middle;"> <?php echo $prospect['name']; ?> </td>
                      <td style="vertical-align: middle;"> <?php echo $prospect['phone']; ?> </td>
                      <td style="vertical-align: middle;"> <?php echo $prospect['email']; ?> </td>
                      <td style="vertical-align: middle;"> <?php echo $prospect['status']; ?> </td>
                      <td style="vertical-align: middle; text-align: justify;">
                        <div class="btn-group" role="group" aria-label="Basic example">
                          <?php
                          if ($prospect['status'] == "open") {
                          ?>
                            <button onclick="location.href='action.php?i=<?php echo $prospect['pid']; ?>&action=convert'" class="btn btn-success" style="width: 40px;"><i class="fas fa-check-circle text-white"></i></button>
                            <a data-toggle="modal" data-id="<?php echo $prospect['pid']; ?>" href="#scheduleModal" class="open-RescheduleModal btn btn-warning" style="width: 40px;"><i class="fas fa-clock text-white"></i></a>
                          <?php
                          }
                          if ($prospect['status'] == "converted") {
                          ?>
                            <button onclick="location.href='action.php?i=<?php echo $prospect['pid']; ?>&action=open'" class="btn btn-warning" style="width: 40px;"><i class="fas fa-times-circle text-white"></i></button>
                          <?php
                          }
                          if ($prospect['status'] == "open") {
                          ?>
                            <button onclick="location.href='action.php?i=<?php echo $prospect['pid']; ?>&action=close'" class="btn btn-info" style="width: 40px;"><i class="fas fa-user-slash text-white"></i></button>
                          <?php
                          }
                          if ($prospect['status'] == "closed") {
                          ?>
                            <button onclick="location.href='action.php?i=<?php echo $prospect['pid']; ?>&action=open'" class="btn btn-dark" style="width: 40px;"><i class="fas fa-user-check text-white"></i></button>
                          <?php
                          }
                          ?>
                        </div>
                      </td>
                    </tr>
                  <?php
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
      <!-- /.container-fluid -->

      <!-- Sticky Footer -->
      <footer class="sticky-footer">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>&copy PrismCode Info Solutions Pvt Ltd <?php date("Y") ?> Made by<a target="_blank" href="https://www.linkedin.com/in/samarth-tandon-0335b0181"> Samarth Tandon </a>  All rights reserved</span>
          </div>
        </div>
      </footer>

    </div>
    <!-- /.content-wrapper -->

  </div>
  <!-- /#wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <!-- Logout Modal-->
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <a class="btn btn-primary" href="logout.php">Logout</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Reschedule Modal-->
  <div class="modal fade" id="scheduleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Schedule</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div class="form-row">
              <div class="form-group col-md-12">
                <input type="text" name="pid" id="reschedule-pid" hidden>
                <label for="date">Date</label>
                <input type="date" class="form-control" id="modal_date" name="date" min="<?php echo date('Y-m-d'); ?>" required>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-12">
                <input type="text" name="pid" id="reschedule-pid" hidden>
                <label for="time">Time</label>
                <input type="time" class="form-control" id="modal_time" name="time" required>
              </div>
            </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <button class="btn btn-primary" type="submit" name="reschedule-submit">Schedule</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <?php
  if (isset($_POST['reschedule-submit'])) {
    extract($_POST);
    $date = $conn->real_escape_string(strip_tags($date));
    $time = $conn->real_escape_string(strip_tags($time));
    $pid = $conn->real_escape_string(strip_tags($pid));
    $sql = "DELETE FROM `schedules` WHERE `pid` = '$pid'";
    if ($conn->query($sql)) {
      $sql = "INSERT INTO `schedules` VALUES (NULL, '$pid', '$date', '$time')";
      if ($conn->query($sql)) {
        echo "<script>alert('Scheduled!');</script>";
      }
    }
  }
  ?>

  <!-- Bootstrap core JavaScript-->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Page level plugin JavaScript-->
  <script src="vendor/chart.js/Chart.min.js"></script>
  <script src="vendor/datatables/jquery.dataTables.js"></script>
  <script src="vendor/datatables/dataTables.bootstrap4.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="js/sb-admin.min.js"></script>

  <!-- Demo scripts for this page-->
  <script src="js/demo/datatables-demo.js"></script>
  <script src="js/demo/chart-area-demo.js"></script>

  <script type="text/javascript">
    $(document).on("click", ".open-RescheduleModal", function() {
      var myProspectId = $(this).data('id');
      $(".modal-body #reschedule-pid").val(myProspectId);
      $('#rescheduleModal').modal('show');
    });
  </script>

</body>

</html>