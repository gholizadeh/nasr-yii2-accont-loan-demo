function LoanCost(id, name, count, amount) {
	var obj = {
		cost_id: id,
		cost_name: name,
		count: count,
		amount: amount,

		toString: function() {
			return '<tr>'
					+ '<td>' + obj.cost_name + ' (به تعداد ' + obj.count + ' معادل  ' + (obj.count*obj.amount) + 'تومان)</td>'
					+ '<td class="text-center"><div class="rem-btn btn" data-id="'+ obj.cost_id +'"><i class="fa fa-trash"></i></div></td>'
					+ '</tr>'; 
		}
	}

	return obj;
}

$(document).ready(function(){
	$loanCostArray = [];

	function StartScript(){
		$.each($selected_costs, function( index, value ) {
			$loanCost = new LoanCost(value.cost_id, $costs[value.cost_id], value.count, $cost_amounts[value.cost_id]);
			addToCosts($loanCost);
		});
		
		checkTable();

		$('.add-cost').on('click', add_cost);
		$(document).on('click', '.rem-btn', function(){
	        rem_cost(this);
	    });
	}

	function add_cost(){
		var cost_id = $('select[name=cost]').val();
		var count = Math.floor($('input[name=count]').val()) ? Math.floor($('input[name=count]').val()) : 0;
		
		if(count==0){
			alertify.warning("تعداد نمی تواند خالی باشد.");
			return;
		}

		$loanCost = new LoanCost(cost_id, $costs[cost_id], count, $cost_amounts[cost_id]);

		addToCosts($loanCost);

		window.onbeforeunload = function() { return true; };
	}

	function addToCosts(loanCost){
		repetitious = false;
		$.each($loanCostArray, function( index, value ) {
			if(value.cost_id == loanCost.cost_id){
				repetitious = true;
				alertify.warning("'"+loanCost.cost_name+"' وجود دارد.");
			}
		});

		if(!repetitious){
			$loanCostArray.push(loanCost);
			addToTable(loanCost);
		}
	}

	function addToTable(loanCost){
		if ($loanCostArray.length == 1)
			$('#selected-table').html(loanCost.toString);
		else
			$('#selected-table').append(loanCost.toString);
	}

	function rem_cost(rem_btn){
		alertify.confirm("مطمئن هستید؟", function (result) {
            if (!result) {
                return;
            }

            var selected = rem_btn.getAttribute('data-id');	
            $loanCostArray = jQuery.grep($loanCostArray, function(value) {
				return value.cost_id != selected;
			});
			var should_rem = rem_btn.parentNode.parentNode;
	    	should_rem.parentNode.removeChild(should_rem);
	    	checkTable();
        });
    }

	function checkTable(){
		if ($loanCostArray.length < 1)
			$('#selected-table').append('<tr><td colspan="2">به این تسهیلات هزینه ای اضافه نشده.</td></tr>');
	}

	$('#loanForm').submit(function() {
		$('#hidden-fields').html('')
		$.each($loanCostArray, function(index,param){
			$.each( param, function(i, n){
				if(typeof n != 'function')
					$('<input />').attr('type', 'hidden').attr('name', 'costs['+index+']['+i+']').attr('value', n).appendTo('#hidden-fields');
			});
		});

		window.onbeforeunload = null;
	});

	StartScript();
});