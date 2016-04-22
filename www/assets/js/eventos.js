$(function() {
	$("#fecha-disp").datepicker({
		dateFormat: 'dd/mm/yy',
		altField: '#fecha',
		altFormat: 'yy-mm-dd',
		changeMonth: true,
		changeYear: true
	});

	var fechaVal = $("#fecha").val();
	if($.trim(fechaVal) !== ''){
		var fechaArr = fechaVal.split('-');
		$("#fecha-disp").datepicker("setDate", fechaArr[2] + "/" + fechaArr[1] + "/" + fechaArr[0]);
	}
});