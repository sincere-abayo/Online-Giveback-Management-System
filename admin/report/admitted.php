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
		<h3 class="card-title">Approved Volunteer </h3>
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
					<col width="20%">
					<col width="20%">
					<col width="25%">
					<col width="15%">
					
				</colgroup>
				<thead>
					<tr class="bg-gradient-dark text-light">
						<th>#</th>
						<th>Date Created</th>
						<th>#Ref-no</th>
						<th>Name</th>
						<th>Status</th>
					
					</tr>
				</thead>
				<tbody>
					<?php 
						$i = 1;
						$qry = $conn->query("SELECT *,concat(lastname,', ',firstname,' ',middlename) as fullname from `volunteer_list`where status = '1' order by concat(lastname,', ',firstname,' ',middlename) asc ");
						while($row = $qry->fetch_assoc()):
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td class=""><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
							<td class=""><p class="m-0 truncate-1"><?php echo $row['id'] ?></p></td>
							<td class=""><p class="m-0 truncate-1"><?php echo $row['fullname'] ?></p></td>
							<td class="text-center">
								<?php 
									switch ($row['status']){
										case 0:
											echo '<span class="rounded-pill badge badge-danger bg-gradient-danger px-3">Pending</span>';
											break;
										case 1:
											echo '<span class="rounded-pill badge badge-success bg-gradient-success px-3">Accepted</span>';
											break;
										case 2:
											echo '<span class="rounded-pill badge badge-warning bg-gradient-warning px-3">Denied</span>';
											break;
										case 3:
											echo '<span class="rounded-pill badge badge-info bg-gradient-info px-3">Dismissed</span>';
											break;
									}
								?>
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
            <h3 class="text-center"><b>Admitted Volunteer</b></h3>
        </div>
        <div class="col-2"></div>
    </div>
</noscript>
<script>
    $(function() {
        $('#update_status').click(function(){
            uni_modal("Update Status of <b><?= isset($roll) ? $roll : "" ?></b>","volunteer/update_status.php?volunteer_id=<?= isset($id) ? $id : "" ?>")
        })
        $('#add_shelter').click(function(){
            uni_modal("Add History Record for <b><?= isset($roll) ? $roll.' - '.$fullname : "" ?></b>","volunteer/manage_shelter.php?volunteer_id=<?= isset($id) ? $id : "" ?>",'mid-large')
        })
        $('.edit_shelter').click(function(){
            uni_modal("Edit History Record of <b><?= isset($roll) ? $roll.' - '.$fullname : "" ?></b>","volunteer/manage_shelter.php?volunteer_id=<?= isset($id) ? $id : "" ?>&id="+$(this).attr('data-id'),'mid-large')
        })
        $('.delete_shelter').click(function(){
			_conf("Are you sure to delete this  Record?","delete_shelter",[$(this).attr('data-id')])
		})
        $('#delete_volunteer').click(function(){
			_conf("Are you sure to delete this Information?","delete_volunteer",['<?= isset($id) ? $id : '' ?>'])
		})
        $('.view_data').click(function(){
			uni_modal("Report Details","volunteer/view_volunteer.php?id="+$(this).attr('data-id'),"mid-large")
		})
       ;
        $('#print').click(function(){
            start_loader()
            $('#volunteer-history').dataTable().fnDestroy()
            var _h = $('head').clone()
            var _p = $('#outprint').clone()
            var _ph = $($('noscript#print-header').html()).clone()
            var _el = $('<div>')
            _p.find('tr.bg-gradient-dark').removeClass('bg-gradient-dark')
            _p.find('tr>td:last-child,tr>th:last-child,colgroup>col:last-child').remove()
            _p.find('.badge').css({'border':'unset'})
            _el.append(_h)
            _el.append(_ph)
            _el.find('title').text('Admitted Volunteer Report')
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

<script>
	
	function delete_volunteer($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_volunteer",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.reload();
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
</script>