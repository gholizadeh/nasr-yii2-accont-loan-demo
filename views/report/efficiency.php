<?php 
$this->title = 'Efficiency Report'; 
?>

<div class="card light-shadow mb-2">
    <div class="card-header pb-0">
		<ul class="nav nav-tabs" id="myTab" role="tablist">
			<li class="nav-item">
				<a class="nav-link active" id="ef-tab" data-toggle="tab" href="#ef-mnt-type" role="tab">Efficiency by Maintenance Type</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" id="ef-mnt-tab" data-toggle="tab" href="#ef-mnt" role="tab">Efficiency by Maintenance</a>
			</li>
		</ul>
    </div>
    <div class="card-body p-0">
		<div class="tab-content border-right border-bottom border-left bg-white" id="reportContent">
			<div class="tab-pane fade show active p-3" id="ef-mnt-type" role="tabpanel">
			    <?= $this->render('_efficiency_mnt_type', [
			        'mnt_types' => $mnt_types,
			        'locations' => $locations
			    ]) ?>
			</div>
			<div class="tab-pane fade p-3" id="ef-mnt" role="tabpanel">
			    <?= $this->render('_efficiency_mnt', [
			        'maintenances' => $maintenances,
			        'locations' => $locations
			    ]) ?>
			</div>
		</div>
    </div>
</div>
<script type="text/javascript">
	function makePostRequest(e, from_id) {
	    e.preventDefault();
	    var jForm = from_id ? $('#'+from_id) : $('<form></form>');
	    jForm.attr('action', $(event.target).attr('href'));
	    jForm.attr('method', 'post');
	    jForm.submit();
	}
</script>