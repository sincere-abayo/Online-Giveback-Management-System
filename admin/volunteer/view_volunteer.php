<?php
if (isset($_GET['id'])) {
    $qry = $conn->query("SELECT *, CONCAT(lastname,', ', firstname) as fullname FROM `volunteer_list` where id = '{$_GET['id']}'");
    if ($qry->num_rows > 0) {
        $res = $qry->fetch_array();
        foreach ($res as $k => $v) {
            if (!is_numeric($k))
                $$k = $v;
        }
    }
}
?>
<div class="content py-2">
    <div class="card card-outline card-navy shadow rounded-0">
        <div class="card-header">
            <h5 class="card-title">Volunteer Details</h5>
            <div class="card-tools">
                <button class="btn btn-sm btn-navy bg-navy btn-flat" type="button" id="add_shelter"><i
                        class="fa fa-plus"></i> Assign Program/Activity</button>
                <button class="btn btn-sm btn-success bg-success btn-flat" type="button" id="print"><i
                        class="fa fa-print"></i> Print</button>
                <a href="./?page=volunteer" class="btn btn-default border btn-sm btn-flat"><i
                        class="fa fa-angle-left"></i> Back to List</a>
            </div>
        </div>
        <div class="card-body">
            <di class="container-fluid" id="outprint">
                <style>
                    #sys_logo {
                        width: 5em;
                        height: 5em;
                        object-fit: scale-down;
                        object-position: center center;
                    }
                </style>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label text-muted">#Ref-no </label>
                            <div class="pl-0"><?= isset($id) ? $id : 'N/A' ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label text-muted">Status</label>
                            <div class="pl-0">
                                <?php
                                switch ($status) {
                                    case 0:
                                        echo '<span class="rounded-pill badge badge-danger bg-gradient-danger px-3">Pending</span>';
                                        break;
                                    case 1:
                                        echo '<span class="rounded-pill badge badge-success bg-gradient-success px-3">Accepted</span>';
                                        break;
                                    case 2:
                                        echo '<span class="rounded-pill badge badge-warning bg-gradient-warning px-3">Denied</span>';
                                        break;

                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <fieldset class="border-bottom">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label text-muted">Name</label>
                                <div class="pl-4"><?= isset($fullname) ? $fullname : 'N/A' ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label text-muted">Contact #</label>
                            <div class="pl-4"><?= isset($contact) ? $contact : 'N/A' ?></div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="email" class="control-label text-muted">E-mail</label>
                            <div class="pl-4"><?= isset($email) ? $email : 'N/A' ?></div>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="motivation" class="control-label text-muted">Motivation</label>
                            <div class="pl-4"><?= isset($motivation) ? $motivation : 'N/A' ?></div>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="comment" class="control-label text-muted">feedback comment</label>
                            <div class="pl-4"><?= isset($comment) ? $comment : 'N/A' ?></div>
                        </div>
                    </div>

                    <fieldset>
                        <legend class="text-muted">Volunteer History</legend>
                        <table class="table table-stripped table-bordered" id="volunteer-history">
                            <colgroup>
                                <col width="5%">
                                <col width="18%">
                                <col width="12%">
                                <col width="12%">
                                <col width="8%">
                                <col width="8%">
                                <col width="8%">
                                <col width="8%">
                                <col width="13%">
                            </colgroup>
                            <thead>
                                <tr class="bg-gradient-dark">
                                    <th class="py-1 text-center">#</th>
                                    <th class="py-1 text-center">Program/Activity</th>
                                    <th class="py-1 text-center">Event Date</th>
                                    <th class="py-1 text-center">Session</th>
                                    <th class="py-1 text-center">Status</th>
                                    <th class="py-1 text-center">End Status</th>
                                    <th class="py-1 text-center">Email</th>
                                    <th class="py-1 text-center">SMS</th>
                                    <th class="py-1 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                $shelter = $conn->query("SELECT a.*,c.name as activity, d.name as program FROM `volunteer_history` a inner join activity_list c on a.activity_id = c.id inner join program_list d on c.program_id = d.id where volunteer_id = '{$id}' order by a.year asc, d.name asc, c.name asc ");
                                while ($row = $shelter->fetch_assoc()):
                                    ?>
                                    <tr>
                                        <td class="px-2 py-1 align-middle text-center"><?= $i++; ?></td>
                                        <td class="px-2 py-1 align-middle">
                                            <small><span
                                                    class="text-primary font-weight-bold"><?= $row['program'] ?></span></small><br>
                                            <small><span class=""><?= $row['activity'] ?></span></small>
                                            <?php if (!empty($row['years'])): ?>
                                                <br><small class="text-muted"><i class="fas fa-info-circle"></i>
                                                    <?= $row['years'] ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-2 py-1 align-middle text-center">
                                            <?php
                                            $event_date = strtotime($row['year']);
                                            $today = strtotime(date('Y-m-d'));
                                            $is_past = $event_date < $today;
                                            ?>
                                            <span class="<?= $is_past ? 'text-muted' : 'text-primary' ?>">
                                                <?= date('M j, Y', $event_date) ?>
                                            </span>
                                            <?php if ($is_past): ?>
                                                <br><small class="text-muted"><i class="fas fa-clock"></i> Past Event</small>
                                            <?php else: ?>
                                                <br><small class="text-success"><i class="fas fa-calendar-check"></i>
                                                    Upcoming</small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-2 py-1 align-middle text-center">
                                            <small><?= !empty($row['s']) ? $row['s'] : 'General Session' ?></small>
                                        </td>
                                        <td class="px-2 py-1 align-middle text-center">
                                            <?php
                                            switch ($row['status']) {
                                                case 0:
                                                    echo '<span class="badge badge-warning">Inactive</span>';
                                                    break;
                                                case 1:
                                                    echo '<span class="badge badge-success">Active</span>';
                                                    break;
                                                default:
                                                    echo '<span class="badge badge-secondary">Unknown</span>';
                                            }
                                            ?>
                                        </td>
                                        <td class="px-2 py-1 align-middle text-center">
                                            <?php
                                            switch ($row['end_status']) {
                                                case 0:
                                                    echo '<span class="badge badge-info">Ongoing</span>';
                                                    break;
                                                case 1:
                                                    echo '<span class="badge badge-secondary">Completed</span>';
                                                    break;
                                                default:
                                                    echo '<span class="badge badge-light">Unknown</span>';
                                            }
                                            ?>
                                        </td>
                                        <td class="px-2 py-1 align-middle text-center">
                                            <?php if (isset($row['email_sent']) && $row['email_sent']): ?>
                                                <span class="badge badge-success" title="Email notification sent">
                                                    <i class="fas fa-envelope"></i> Sent
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary" title="Email notification not sent">
                                                    <i class="fas fa-envelope"></i> Not Sent
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-2 py-1 align-middle text-center">
                                            <?php if (isset($row['sms_sent']) && $row['sms_sent']): ?>
                                                <span class="badge badge-success" title="SMS notification sent">
                                                    <i class="fas fa-sms"></i> Sent
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary" title="SMS notification not sent">
                                                    <i class="fas fa-sms"></i> Not Sent
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-2 py-1 align-middle text-center">
                                            <button type="button"
                                                class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon"
                                                data-toggle="dropdown">
                                                Action
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <div class="dropdown-menu" role="menu">
                                                <a class="dropdown-item edit_shelter" href="javascript:void(0)"
                                                    data-id="<?php echo $row['id'] ?>"><span
                                                        class="fa fa-edit text-primary"></span> Update</a>
                                                <a class="dropdown-item delete_shelter" href="javascript:void(0)"
                                                    data-id="<?php echo $row['id'] ?>"><span
                                                        class="fa fa-trash text-danger"></span> Delete</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </fieldset>
        </div>
    </div>
</div>
</div>

<noscript id="print-header">
    <div class="row bg-dark">
        <div class="col-2 d-flex justify-content-center align-items-center">
            <img src="<?= validate_image($_settings->info('logo')) ?>" class="img-circle" id="sys_logo"
                alt="System Logo">
        </div>
        <div class="col-8">
            <h4 class="text-center"><b><?= $_settings->info('name') ?></b></h4>
            <h3 class="text-center"><b>Volunteer Profile</b></h3>
        </div>
        <div class="col-2"></div>
    </div>
</noscript>
<script>
    $(function () {
        $('#update_status').click(function () {
            uni_modal("Update Status of <b><?= isset($roll) ? $roll : "" ?></b>",
                "volunteer/update_status.php?volunteer_id=<?= isset($id) ? $id : "" ?>")
        })
        $('#add_shelter').click(function () {
            uni_modal("Assign  program/Activity on  <b><?= isset($roll) ? $roll . ' - ' . $fullname : "" ?></b>",
                "volunteer/manage_shelter.php?volunteer_id=<?= isset($id) ? $id : "" ?>", 'mid-large')
        })
        $('.edit_shelter').click(function () {
            uni_modal("Edit History Record of <b><?= isset($roll) ? $roll . ' - ' . $fullname : "" ?></b>",
                "volunteer/manage_shelter.php?volunteer_id=<?= isset($id) ? $id : "" ?>&id=" + $(this)
                    .attr('data-id'), 'mid-large')
        })
        $('.delete_shelter').click(function () {
            _conf("Are you sure to delete this Record?", "delete_shelter", [$(this).attr('data-id')])
        })
        $('#delete_volunteer').click(function () {
            _conf("Are you sure to delete this Information?", "delete_volunteer", [
                '<?= isset($id) ? $id : '' ?>'
            ])
        })
        $('.view_data').click(function () {
            uni_modal("Report Details", "volunteer/view_volunteer.php?id=" + $(this).attr('data-id'),
                "mid-large")
        })
        $('.table td, .table th').addClass('py-1 px-2 align-middle')
        $('.table').dataTable({
            columnDefs: [{
                orderable: true,
                targets: 3
            }],
        });
        $('#print').click(function () {
            start_loader()
            $('#volunteer-history').dataTable().fnDestroy()
            var _h = $('head').clone()
            var _p = $('#outprint').clone()
            var _ph = $($('noscript#print-header').html()).clone()
            var _el = $('<div>')
            _p.find('tr.bg-gradient-dark').removeClass('bg-gradient-dark')
            _p.find('tr>td:last-child,tr>th:last-child,colgroup>col:last-child').remove()
            _p.find('.badge').css({
                'border': 'unset'
            })
            _el.append(_h)
            _el.append(_ph)
            _el.find('title').text('Volunteer Records')
            _el.append(_p)


            var nw = window.open('', '_blank', 'width=1000,height=900,top=50,left=200')
            nw.document.write(_el.html())
            nw.document.close()
            setTimeout(() => {
                nw.print()
                setTimeout(() => {
                    nw.close()
                    end_loader()
                    $('.table').dataTable({
                        columnDefs: [{
                            orderable: true,
                            targets: 5
                        }],
                    });
                }, 300);
            }, (750));


        })
    })

    function delete_shelter($id) {
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=delete_shelter",
            method: "POST",
            data: {
                id: $id
            },
            dataType: "json",
            error: err => {
                console.log(err)
                alert_toast("An error occured.", 'error');
                end_loader();
            },
            success: function (resp) {
                if (typeof resp == 'object' && resp.status == 'success') {
                    location.reload();
                } else {
                    alert_toast("An error occured.", 'error');
                    end_loader();
                }
            }
        })
    }

    function delete_volunteer($id) {
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=delete_volunteer",
            method: "POST",
            data: {
                id: $id
            },
            dataType: "json",
            error: err => {
                console.log(err)
                alert_toast("An error occured.", 'error');
                end_loader();
            },
            success: function (resp) {
                if (typeof resp == 'object' && resp.status == 'success') {
                    location.href = "./?page=volunteer";
                } else {
                    alert_toast("An error occured.", 'error');
                    end_loader();
                }
            }
        })
    }
</script>