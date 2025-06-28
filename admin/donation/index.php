
<style>
    .img-thumb-path{
        width:100px;
        height:80px;
        object-fit:scale-down;
        object-position:center center;
    }
</style>

<div class="card card-outline card-primary rounded-0 shadow">
	<div class="card-header">
		<h3 class="card-title">Manage Donations Offered</h3>
		<div class="card-tools">
			<button class="btn btn-sm btn-success bg-success btn-flat" type="button" id="print"><i class="fa fa-print"></i> Print</button>
            
		</div>
	</div>

 <div class="card-body">
            <div class="container-fluid" id="outprint">
                <style>
                    #sys_logo{
                        width:5em;
                        height:5em;
                        object-fit:scale-down;
                        object-position:center center;
                    }
                </style>

		<div class="container-fluid">
        <div class="container-fluid">
			<table class="table table-bordered table-hover table-striped">
				<colgroup>
					<col width="5%">
					<col width="15%">
					<col width="15%">
					<col width="20%">
					<col width="15%">
					<col width="10%">
					<col width="15%">
					
				</colgroup>
				<thead>
					<tr class="bg-gradient-dark text-light">
						<th>#</th>
						<th>Full Name</th>
                        <th>Serv-provider</th>
						<th>Phone Number</th>
						<th>Amount(Frw)</th>
						<th>Status</th>
						<th>Action</th>
					
					</tr>
				</thead>
				<tbody>
					<?php 
						$i = 1;
						$qry = $conn->query("SELECT *,concat(fullname) as fullname from `donation`  ");
						while($row = $qry->fetch_assoc()):
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td class=""><p class="m-0 truncate-1"><?php echo $row['fullname'] ?></p></td>
							<td class=""><p class="m-0 truncate-1"><?php echo $row['provider'] ?></p></td>
							<td class=""><p class="m-0 truncate-1"><?php echo $row['phone_number'] ?></p></td>
							<td class=""><p class="m-0 truncate-1"><?php echo $row['amount'] ?></p></td>
		                   
                            <td>
        <span id="status-<?php echo $row['id']; ?>" class="badge badge-<?php echo $row['status'] == 'Paid' ? 'success' : 'warning'; ?>">
            <?php echo $row['status']; ?>
        </span>
    </td>
    <td>
        <button class="btn btn-sm btn-success" data-id="<?php echo $row['id']; ?>" onclick="setToPaid(<?php echo $row['id']; ?>)"> Paid</button>
        <button class="btn btn-sm btn-danger" data-id="<?php echo $row['id']; ?>" onclick="setToUnPaid(<?php echo $row['id']; ?>)"> UnPaid</button>
    </td>

						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
		</div>
	</div>
</div>

<noscript id="print-header">
    <div class="row bg-dark">
        <div class="col-2 d-flex justify-content-center align-items-center">
            <img src="<?= validate_image($_settings->info('logo')) ?>" class="img-circle" id="sys_logo" alt="System Logo">
        </div>
        <div class="col-8">
            <h4 class="text-center"><b><?= $_settings->info('name') ?></b></h4>
            <h3 class="text-center"><b>Donations Report</b></h3>
        </div>
        <div class="col-2"></div>
    </div>
   
<noscript id="print-header">
    <div class="row bg-dark">
        <div class="col-2 d-flex justify-content-center align-items-center">
            <img src="<?= validate_image($_settings->info('logo')) ?>" class="img-circle" id="sys_logo" alt="System Logo">
        </div>
        <div class="col-8">
            <h4 class="text-center"><b><?= $_settings->info('name') ?></b></h4>
            <h3 class="text-center"><b> Donation Report</b></h3>
        </div>
        <div class="col-2"></div>
    </div>
</noscript>
<script>
    $(function() {
        $('#update_status').click(function(){
            uni_modal("Update Status of <b><?= isset($id) ? $id : "" ?></b>","report/donation.php?id=<?= isset($id) ? $id : "" ?>")
        })
        $('#add_shelter').click(function(){
            uni_modal("Add History Record for <b><?= isset($id) ? $fullname : "" ?></b>","report/donation.php?id=<?= isset($id) ? $id : "" ?>",'mid-large')
        })
        $('.edit_shelter').click(function(){
            uni_modal("Edit History Record of <b><?= isset($id) ? $fullname : "" ?></b>","report/donation.php?id=<?= isset($id) ? $id : "" ?>&id="+$(this).attr('data-id'),'mid-large')
        })
        $('.delete_shelter').click(function(){
			_conf("Are you sure to delete this  Record?","delete_shelter",[$(this).attr('data-id')])
		})
        $('#delete_volunteer').click(function(){
			_conf("Are you sure to delete this  Information?","report/donation.php",['<?= isset($id) ? $id : '' ?>'])
		})
        $('.view_data').click(function(){
			uni_modal("Report Details","report/donation.php?id="+$(this).attr('data-id'),"mid-large")
		})
       ;
        $('#print').click(function(){
            start_loader()
            $('#donation').dataTable().fnDestroy()
            var _h = $('head').clone()
            var _p = $('#outprint').clone()
            var _ph = $($('noscript#print-header').html()).clone()
            var _el = $('<div>')
            _p.find('tr.bg-gradient-dark').removeClass('bg-gradient-dark')
            _p.find('tr>td:last-child,tr>th:last-child,colgroup>col:last-child').remove()
            _p.find('.badge').css({'border':'unset'})
            _el.append(_h)
            _el.append(_ph)
            _el.find('title').text(' Donation Report')
            _el.append(_p)


            var nw = window.open('','_blank','width=1000,height=900,top=50,left=200')
                nw.document.write(_el.html())
                nw.document.close()
                setTimeout(() => {
                    nw.print()
                    setTimeout(() => {
                        nw.close()
                        end_loader()
                        $('.table').dataTable({
                            columnDefs: [
                                { orderable: false, targets: 0 }
                            ],
                        });
                    }, 300);
                }, (750));
                
            
        })
    })
    
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function setToPaid(itemId) {
    console.log("Sending AJAX request to update status for item ID: " + itemId);
    $.post('http://localhost/GMS/status+.php', { id: itemId }, function(response) {
        console.log("Response received: " + response);
        location.reload(); // Reload the page to reflect changes
        var res = JSON.parse(response);
        if (res.success) {
            alert('Status updated to Paid.');
            // Update the status badge text and class dynamically
            $('#status-' + itemId).text('Paid').removeClass('badge-warning').addClass('badge-success');
            // Disable the button after successful update
            $('button[onclick="setToPaid(' + itemId + ')"]').prop('disabled', true).text('Paid').removeClass('bg-green-500 hover:bg-green-700').addClass('bg-gray-400 cursor-not-allowed');
        } else {
            alert('Failed to update status: ' + res.message);
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        alert('Error occurred while updating status: ' + textStatus + ', ' + errorThrown);
        console.error('AJAX error:', textStatus, errorThrown);
    });
}
</script>
<script>
function setToUnPaid(itemId) {
    console.log("Sending AJAX request to update status for item ID: " + itemId);
    $.post('http://localhost/GMS/status-.php', { id: itemId }, function(response) {
        console.log("Response received: " + response);
        location.reload(); // Reload the page to reflect changes
        var res = JSON.parse(response);
        if (res.success) {
            alert('Status updated to UnPaid.');
            // Update the status badge text and class dynamically
            $('#status-' + itemId).text('Paid').removeClass('badge-warning').addClass('badge-success');
            // Disable the button after successful update
            $('button[onclick="setToUnPaid(' + itemId + ')"]').prop('disabled', true).text('UnPaid').removeClass('bg-red-500 hover:bg-red-700').addClass('bg-gray-400 cursor-not-allowed');
        } else {
            alert('Failed to update status: ' + res.message);
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        alert('Error occurred while updating status: ' + textStatus + ', ' + errorThrown);
        console.error('AJAX error:', textStatus, errorThrown);
    });
}
</script>

