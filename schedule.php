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

$sql = "SELECT `uid`, `name` FROM `users`";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
  $allusers[$row['uid']] = $row['name'];
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

  <title>My Schedule | Prospect Management System</title>

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
          <a class="dropdown-item" href="#">Change Password</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">Logout</a>
        </div>
      </li>
    </ul>

  </nav>

  <div id="wrapper">

    <!-- Sidebar -->
    <ul class="sidebar navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="home.php">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Dashboard</span>
        </a>
      </li>
      <li class="nav-item active">
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
            <a href="home.php">Dashboard</a>
          </li>
          <li class="breadcrumb-item active">Scheduled Follow-ups</li>
        </ol>
        <?php
        $uid = $current_user['uid'];
        $today = date("Y-m-d");
        $sql = "SELECT `prospects`.`pid`, `prospects`.`name`, `prospects`.`phone`, `prospects`.`email`, `schedules`.`time` FROM `prospects`,`schedules` WHERE `schedules`.pid = `prospects`.pid AND `schedules`.date = '$today' AND `prospects`.`uid` = '$uid' AND `prospects`.`status` = 'open'";
        $result = $conn->query($sql);
        $prospects = array();
        while ($row = $result->fetch_assoc()) {
          $prospects[$row['pid']] = $row;
        }
        $pid = array_keys($prospects);
        $reports = array();
        if (!empty($pid)) {
          $sql = "SELECT * FROM `reports` WHERE `pid` IN (" . implode(',', $pid) . ") ORDER BY `pid`, `timestamp` DESC";
          $result = $conn->query($sql);
                   $i = 0;
          while ($row = $result->fetch_assoc()) {
            $reports[$row['pid']][$i] = array("timestamp" => $row['timestamp'], "comments" => $row['comments'], "user" => $row['uid']);
            $i++;
          }
        }
        ?>

        <!-- DataTables Example -->
        <div class="card mb-3">
          <div class="card-header">
            <i class="fas fa-users"></i>
            Followups for <?php echo date("d F Y"); ?></div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered text-center" id="dataTable" width="100%" cellspacing="0">
                <thead>
                  <tr>
                    <th style="vertical-align: middle;">Name</th>
                    <th style="vertical-align: middle;">Mobile No.</th>
                    <th style="vertical-align: middle;">Email</th>
                    <th style="vertical-align: middle;">Time</th>
                    <th style="vertical-align: middle;">Last Followup</th>
                    <th style="vertical-align: middle;">Last Comment</th>
                    <th style="vertical-align: middle;">Actions</th>
                  </tr>
                </thead>
                <tfoot>
                  <tr>
                    <th style="vertical-align: middle;">Name</th>
                    <th style="vertical-align: middle;">Mobile No.</th>
                    <th style="vertical-align: middle;">Email</th>
                    <th style="vertical-align: middle;">Time</th>
                    <th style="vertical-align: middle;">Last Followup</th>
                    <th style="vertical-align: middle;">Last Comment</th>
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
                      <td style="vertical-align: middle;"> <?php echo date("h:i A", strtotime($prospect['time'])); ?> </td>
                      <td style="vertical-align: middle;"> <?php if (!empty($reports[$prospect['pid']][0])) {
                                                              echo date("d F Y h:i A", strtotime($reports[$prospect['pid']][0]['timestamp']));
                                                            } else {
                                                              echo "-";
                                                            } ?> </td>
                      <td style="vertical-align: middle;"> <?php if (!empty($reports[$prospect['pid']][0])) {
                                                              echo $reports[$prospect['pid']][0]['comments'];
                                                            } else {
                                                              echo "-";
                                                            } ?> </td>
                      <td style="vertical-align: middle; text-align: justify;">
                        <div class="btn-group" role="group" aria-label="Basic example">
                          <a href="mailto:<?php echo $prospect['email']; ?>" class="btn btn-dark" style="width: 40px;"><i class="fas fa-envelope text-white"></i></a>
                          <a href="tel:<?php echo $prospect['phone']; ?>" class="btn btn-secondary" style="width: 40px;"><i class="fas fa-phone text-white"></i></a>
                          <a data-toggle="modal" data-id="<?php echo $prospect['pid']; ?>" href="#scheduleModal" class="open-RescheduleModal btn btn-primary" style="width: 40px;"><i class="fas fa-clock text-white"></i></a>
                          <a data-toggle="modal" data-id="<?php echo $prospect['pid']; ?>" href="#commentModal" class="open-AddCommentModal btn btn-warning" style="width: 40px;"><i class="fas fa-comment text-white"></i></a>
                          <a data-toggle="modal" href="#Modal<?php echo $prospect['pid']; ?>" class="open-ModalDetails<?php echo $prospect['pid']; ?> btn btn-info" style="width: 40px;"><i class="fas fa-info text-white"></i></a>
                          <button onclick="location.href='action.php?i=<?php echo $prospect['pid']; ?>&action=convert'" class="btn btn-success" style="width: 40px;"><i class="fas fa-check-circle text-white"></i></button>
                          <button onclick="location.href='action.php?i=<?php echo $prospect['pid']; ?>&action=close'" class="btn btn-danger" style="width: 40px;"><i class="fas fa-user-slash text-white"></i></button>
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

  <?php
  if (isset($_POST['comment-submit'])) {
    extract($_POST);
    $comment = $conn->real_escape_string(strip_tags($comment));
    $pid = $conn->real_escape_string(strip_tags($pid));
    $timestamp = date("Y-m-d H:i:s", time());
    $uid = $current_user['uid'];
    $sql = "INSERT INTO `reports` VALUES (NULL, '$pid', '$uid', '$timestamp', '$comment')";
    if ($conn->query($sql)) {
      echo "<script>alert('Comment Added!'); window.location.replace('schedule.php');</script>";
    }
  }

  if (isset($_POST['reschedule-submit'])) {
    extract($_POST);
    $date = $conn->real_escape_string(strip_tags($date));
    $time = $conn->real_escape_string(strip_tags($time));
    $pid = $conn->real_escape_string(strip_tags($pid));
    $sql = "DELETE FROM `schedules` WHERE `pid` = '$pid'";
    if ($conn->query($sql)) {
      $sql = "INSERT INTO `schedules` VALUES (NULL, '$pid', '$date', '$time')";
      if ($conn->query($sql)) {
        echo "<script>alert('Rescheduled!'); window.location.replace('schedule.php');</script>";
      }
    }
  }
  ?>

  <?php
  foreach ($reports as $pid => $arrays) {
  ?>
    <!-- Details Modal -->
    <div class="modal fade" id="Modal<?php echo $pid ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Follow-up History of <?php echo $prospects[$pid]['name']; ?></h5>
            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="table-responsive">
              <table class="table table-bordered text-center" id="dataTable" width="100%" cellspacing="0">
                <thead>
                  <tr>
                    <th style="vertical-align: middle;">Timestamp</th>
                    <th style="vertical-align: middle;">Comment</th>
                    <th style="vertical-align: middle;">Manager</th>
                  </tr>
                </thead>
                <tfoot>
                  <tr>
                    <th style="vertical-align: middle;">Timestamp</th>
                    <th style="vertical-align: middle;">Comment</th>
                    <th style="vertical-align: middle;">Manager</th>
                  </tr>
                </tfoot>
                <tbody>
                  <?php
                  foreach ($arrays as $value) {
                  ?>
                    <tr>
                      <td style="vertical-align: middle;"> <?php echo $value['timestamp']; ?> </td>
                      <td style="vertical-align: middle;"> <?php echo $value['comments']; ?> </td>
                      <td style="vertical-align: middle;"> <?php echo $allusers[$value['user']]; ?> </td>
                    </tr>
                  <?php
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          </div>
        </div>
      </div>
    </div>
    <script type="text/javascript">
      $(document).on("click", ".open-ModalDetails<?php echo $pid; ?>", function() {
        $('#Modal<?php echo $pid; ?>').modal('show');
      });
    </script>
  <?php
  }
  ?>
  <!-- Comment Modal-->
  <div class="modal fade" id="commentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Add Comment</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div class="form-row">
              <div class="form-group col-md-12">
                <input type="text" name="pid" id="comment-pid" hidden>
                <label for="date">Comment</label>
                <textarea name="comment" class="form-control" rows="5" maxlength="500" required></textarea>
              </div>
            </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <button class="btn btn-primary" type="submit" name="comment-submit">Add</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Reschedule Modal-->
  <div class="modal fade" id="scheduleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Reschedule</h5>
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
                <input type="date" class="form-control" name="date" min="<?php echo date('Y-m-d'); ?>" required>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-12">
                <input type="text" name="pid" id="reschedule-pid" hidden>
                <label for="time">Time</label>
                <input type="time" class="form-control" name="time" required>
              </div>
            </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <button class="btn btn-primary" type="submit" name="reschedule-submit">Reschedule</button>
          </form>
        </div>
      </div>
    </div>
  </div>

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
    $(document).on("click", ".open-AddCommentModal", function() {
      var myProspectId = $(this).data('id');
      $(".modal-body #comment-pid").val(myProspectId);
      $('#commentModal').modal('show');
    });

    $(document).on("click", ".open-RescheduleModal", function() {
      var myProspectId = $(this).data('id');
      $(".modal-body #reschedule-pid").val(myProspectId);
      $('#rescheduleModal').modal('show');
    });
  </script>

</body>

</html>