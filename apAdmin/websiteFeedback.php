<div class="content-wrapper">
  <div class="container-fluid">
    <div class="row pt-2 pb-2">
      <div class="col-sm-9">
        <h4 class="page-title">Website Contact Us Feedback</h4>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-info nav-justified">
              <?php
              extract($_REQUEST);
              $active_table = "";
              $closed_table = "";
              $tab = 0;
              if ($tab == 0) {
                $active_table = "active";
              } else if ($tab == 1) {
                $withDeveloper = "active";
              } else if ($tab == 2) {
                $closed_table = "active";
              } else {
                $active_table = "active";
              }
              ?>
              <li class="nav-item">
                <a class="nav-link <?php echo $active_table; ?>" data-toggle="tab" href="#active_tab"><i class="fa fa-spinner"></i> <span>Pending</span></a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php echo $closed_table; ?>" data-toggle="tab" href="#closed_tab"><i class="fa fa-check"></i> <span>Solved</span></a>
              </li>
            </ul>
            <div class="tab-content">
              <div id="active_tab" class=" tab-pane <?php echo $active_table; ?> show">
                <div class="table-responsive">
                  <table id="pendingTable" class="table table-bordered">
                    <thead>
                      <tr>
                        <th>Sr.No</th>
                        <th> Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>City</th>
                        <th>Subject</th>
                        <th>Created Date</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $i = 1;
                      $q = $d->select("contact_us", "status = '1'");
                      while ($data = mysqli_fetch_array($q)) {
                      ?>
                        <tr>
                          <td><?php echo $i++; ?></td>
                          <td><?php echo $data['name']; ?></td>
                          <td><?php echo $data['email']; ?></td>
                          <td><?php echo $data['mobile']; ?></td>
                          <td><?php echo $data['city']; ?></td>
                          <td><?php echo $data['subject']; ?></td>
                          <td><?php echo $data['add_date']; ?></td>
                          <td>
                            <button data-toggle="modal" onclick="get_id('<?php echo $data['id']; ?>');" id="tooltip" data-target="#ajaxModal" class="btn btn-sm btn-success-new"><i class="fa fa-info-circle"></i></button>
                            <?php
                            if (empty($data['reply'])) {
                            ?>
                              <button data-toggle="modal" onclick="get_reply_id('<?php echo $data['id']; ?>');" id="tooltip" data-target="#ajaxModal_reply" class="btn btn-sm btn-success-new"><i class="fa fa-reply"></i></button>
                            <?php } ?>
                            <form class="d-inline-block" action="controller/replyController.php" method="post">
                              <input type="hidden" name="contact_us_id" value="<?php echo $data['id']; ?>">
                              <input type="hidden" name="forwardtoDeveloper">
                              <button type="submit" title="Forward as Solved?" name="" class="btn btn-dark btn-sm form-btn"><i class="fa fa-arrow-right fa-lg"> </i></button>
                            </form>
                          </td>
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              </div>
              <?php if ($tab = 'closed_tab') { ?>
                <div id="closed_tab" class=" tab-pane <?php echo $closed_table; ?> fade show">
                  <div class="table-responsive">
                    <table id="example" class="table table-bordered">
                      <thead>
                        <tr>
                          <th>Sr.No</th>
                          <th> Name</th>
                          <th> Email</th>
                          <th>Mobile</th>
                          <th>City</th>
                          <th>Subject</th>
                          <th>Created Date</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $i = 1;
                        $q = $d->select("contact_us", "status = '0'");
                        while ($data = mysqli_fetch_array($q)) {
                        ?>
                          <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo $data['name']; ?></td>
                            <td><?php echo $data['email']; ?></td>
                            <td><?php echo $data['mobile']; ?></td>
                          <td><?php echo $data['city']; ?></td>
                          <td><?php echo $data['subject']; ?></td>
                          <td><?php echo $data['add_date']; ?></td>
                            <td>
                              <button data-toggle="modal" onclick="get_id('<?php echo $data['id']; ?>');" id="tooltip" data-target="#ajaxModal" class="btn btn-sm btn-success-new"><i class="fa fa-info-circle"></i></button>
                            </td>
                          </tr>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="ajaxModal">
  <div class="modal-dialog">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Info</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-content" id="setAjaxData">

      </div>

    </div>
  </div>
</div>

<div class="modal fade" id="ajaxModal_reply">
  <div class="modal-dialog">
    <div class="modal-content border-primary">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white">Reply User</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-content" id="setAjaxData_reply">
      </div>
    </div>
  </div>
</div>
<script src="assets/js/jquery.min.js"></script>
<script type="text/javascript">
  function get_id(id) {
    $.ajax({
        url: 'ajax/show_user_details.php',
        type: 'POST',
        data: {
          id: id
        }
      })
      .done(function(response) {
        $('#setAjaxData').html(response);
      });
  }
  function get_reply_id(id) {
    $.ajax({
        url: 'ajax/reply_user.php',
        type: 'POST',
        data: {
          id: id
        }
      })
      .done(function(response) {
        $('#setAjaxData_reply').html(response);
      });
  }
  $(document).ready(function() {
    $.fn.dataTable.ext.errMode = 'none';
    $('#pendingTable, #example').DataTable({
        lengthChange: false,
        responsive: true,
        autoWidth: false,
        searching: true,
        ordering: true,
        processing: true,
        destroy: true,
        lengthMenu: [
            [10, 20, 30, 50],
            [10, 20, 30, 50]
        ],
        pageLength: 10,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copy',
                text: '<i class="fa fa-copy"></i> Copy',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'csv',
                text: '<i class="fa fa-file-alt"></i> CSV',
                exportOptions: {
                    columns: ':not(:last-child)',
                    format: {
                        body: function(data, row, column, node) {
                            return '\u200C' + data; 
                        }
                    }
                }
            },
            {
                extend: 'excel',
                text: '<i class="fa fa-file-excel"></i> Excel',
                exportOptions: {
                    columns: ':not(:last-child)',
                    format: {
                        body: function(data, row, column, node) {
                            return '\u200C' + data; 
                        }
                    }
                }
            },
            {
                
                extend: 'pdf',
                text: '<i class="fa fa-file-pdf"></i> PDF',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: {
                    columns: ':not(:last-child)'
                },
                customize: function(doc) {
                }
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print"></i> Print',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            }
        ]
    });
});



</script>