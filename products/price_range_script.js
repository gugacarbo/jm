$(document).ready(function(){
	
	$('#price-range-submit').hide();

	$("#SearchMinVal,#SearchMaxVal").on('change', function () {

	  $('#price-range-submit').show();

	  var min_price_range = parseInt($("#SearchMinVal").val());

	  var max_price_range = parseInt($("#SearchMaxVal").val());

	  if (min_price_range > max_price_range) {
		$('#SearchMaxVal').val(min_price_range);
	  }

	  $("#slider-range").slider({
		values: [min_price_range, max_price_range]
	  });
	  
	});


	$("#SearchMinVal,#SearchMaxVal").on("paste keyup input", function () {                                        

	  $('#price-range-submit').show();

	  var min_price_range = parseInt($("#SearchMinVal").val());

	  var max_price_range = parseInt($("#SearchMaxVal").val());
	  
	  if(min_price_range == max_price_range){

			max_price_range = min_price_range + 100;
			
			$("#SearchMinVal").val(min_price_range);		
			$("#SearchMaxVal").val(max_price_range);
	  }

	  $("#slider-range").slider({
		values: [min_price_range, max_price_range]
	  });

	});


	$(function () {
	  $("#slider-range").slider({
		range: true,
		orientation: "horizontal",
		min: 0,
		max: 10000,
		values: [0, 10000],
		step: 10,

		slide: function (event, ui) {
		  if (ui.values[0] == ui.values[1]) {
			  return false;
		  }
		  
		  $("#SearchMinVal").val(ui.values[0]);
		  $("#SearchMaxVal").val(ui.values[1]);
		}
	  });

	  $("#SearchMinVal").val($("#slider-range").slider("values", 0));
	  $("#SearchMaxVal").val($("#slider-range").slider("values", 1));

	});

	$("#slider-range,#price-range-submit").click(function () {

	  var min_price = $('#SearchMinVal').val();
	  var max_price = $('#SearchMaxVal').val();

	  $("#searchResults").text("Here List of products will be shown which are cost between " + min_price  +" "+ "and" + " "+ max_price + ".");
	});

});