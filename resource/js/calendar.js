$(document).ready(function() {
	var getData = $("#getData").text();
	var siteUrl = $("#siteUrl").text();
	var getResaCalURL = siteUrl + "/resa/getCalendar";
	var getResaCostURL = siteUrl + "/resa/getCost";
	var getResaBillURL = siteUrl + "/resa/getBill";
	var getCostURL = siteUrl + "/cost/get";
	var createURL = siteUrl + "/resa/create";
	var deleteURL = siteUrl + "/resa/delete";
	var $defer = 
		$.ajax({
			url : getResaCalURL,
			type : "GET",
			data : getData,
			dataType : "html",
			cache : false,
			success : function(result) {
				$("#calendarContent").append(result);
				$.ajax({
					url : getResaCostURL,
					type : "GET",
					data : getData,
					dataType : "html",
					cache : false,
					success : function(result) {
						var cost = jQuery.parseJSON(result);
						for (var key in cost) {
							if (key=="sum") {
								$(".cost").html(cost.sum.resa);
								$(".totalDepassement").html(cost.sum.depassement);
								$(".total").html(cost.sum.total);
							} else if (key=="children") {
								for (var childId in cost.children) {
									$("."+childId+"-cost").html(cost.children[childId].resaStr);
			                        $("."+childId+"-costDepassement").html(cost.children[childId].depassementStr);
									$("."+childId+"-total").html(cost.children[childId].total);
								}
							}
						}
					}
				});
				$.ajax({
					url : getResaBillURL,
					type : "GET",
					data : getData,
					dataType : "html",
					cache : false,
					success : function(result) {
						var bill = jQuery.parseJSON(result);
						$(".bill").html(bill);
					}
				});
			}
		});
	$(".period").live("click", function() {
		var text = $(this).text();
		text = $.trim(text);
		var data = text.split("-");
		var $defer = 
			$.ajax({
				url : createURL,
				type : "POST",
				cache : false,
				data : {
				year : data[0],
				month : data[1],
				day : data[2],
				period : data[3],
				child : data[4]
			},
			success : function(result) {
				var resas = jQuery.parseJSON(result);
				$.each(resas, function(property, value) {
					var dateStr=value.date.split(" ");
					dateStr=dateStr[0].split("-");
					var cellStr = dateStr[0]+"-"+dateStr[1]+"-"+dateStr[2].replace(/^0+/, '')+"-"+value.period_id+"-"+value.child_id;
					var cell = $("p.content:contains("+cellStr+")").parent();
					var type = value.resa_type;
					if (type==3) {
						//cell.css('background-color', '#A6233C');
						cell.attr("class", "period_3");
					} else {
						//cell.css('background-color', '#ACF28A');
						cell.attr("class", "period_1");
					}
				});

				$.ajax({
					url : getResaCostURL,
					type : "GET",
					data : getData,
					dataType : "html",
					cache : false,
					success : function(result) {
						var cost = jQuery.parseJSON(result);
						for (var key in cost) {
							if (key=="sum") {
								$(".cost").html(cost.sum.resa);
								$(".totalDepassement").html(cost.sum.depassement);
								$(".total").html(cost.sum.total);
							} else if (key=="children") {
								for (var childId in cost.children) {
									$("."+childId+"-cost").html(cost.children[childId].resaStr);
			                        $("."+childId+"-costDepassement").html(cost.children[childId].depassementStr);
									$("."+childId+"-total").html(cost.children[childId].total);
								}
							}
						}
					}
				});
			},
			error : function(result) {
//				alert("failure period "+result);
			}
			});
	});

	$(".period_1").live("click", function() {	// periode reservee
		var text = $(this).text();
		text = $.trim(text);
		var data = text.split("-");
		var aObj = $(this);
		var $defer = 
			$.ajax({
				url : deleteURL,
				type : "POST",
				cache : false,
				data : {
				year : data[0],
				month : data[1],
				day : data[2],
				period : data[3],
				child : data[4]
			},
			success : function(result) {
//				alert("success period1 "+result);
				aObj.attr("class", "period");
				$.ajax({
					url : getResaCostURL,
					type : "GET",
					data : getData,
					dataType : "html",
					cache : false,
					success : function(result) {
						var cost = jQuery.parseJSON(result);
						for (var key in cost) {
							if (key=="sum") {
								$(".cost").html(cost.sum.resa);
								$(".totalDepassement").html(cost.sum.depassement);
								$(".total").html(cost.sum.total);
							} else if (key=="children") {
								for (var childId in cost.children) {
									$("."+childId+"-cost").html(cost.children[childId].resaStr);
			                        $("."+childId+"-costDepassement").html(cost.children[childId].depassementStr);
									$("."+childId+"-total").html(cost.children[childId].total);
								}
							}
						}
					}
				});
			},
			error : function(result) {
//				alert("failure period1 "+result);
			}
			});
	});

	$(".period_3").live("click", function() {	// periode en depassement
		var text = $(this).text();
		text = $.trim(text);
		var data = text.split("-");
		var aObj = $(this);
//		alert("period3 "+aObj);
		var $defer = 
			$.ajax({
				url : deleteURL,
				type : "POST",
				cache : false,
				data : {
				year : data[0],
				month : data[1],
				day : data[2],
				period : data[3],
				child : data[4]
			},
			success : function(result) {
//				alert("success period3 "+result);
				aObj.attr("class", "period");
				$.ajax({
					url : getResaCostURL,
					type : "GET",
					data : getData,
					dataType : "html",
					cache : false,
					success : function(result) {
						var cost = jQuery.parseJSON(result);
						for (var key in cost) {
							if (key=="sum") {
								$(".cost").html(cost.sum.resa);
								$(".totalDepassement").html(cost.sum.depassement);
								$(".total").html(cost.sum.total);
							} else if (key=="children") {
								for (var childId in cost.children) {
									$("."+childId+"-cost").html(cost.children[childId].resaStr);
			                        $("."+childId+"-costDepassement").html(cost.children[childId].depassementStr);
									$("."+childId+"-total").html(cost.children[childId].total);
								}
							}
						}
					}
				});
			},
			error : function(result) {
//				alert("failure period3 "+result);
			}
			});

	});

});
