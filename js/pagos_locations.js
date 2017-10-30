$(document).ready(function(){
	var editId = -1;
	var deleteId = -1;

	$(".button-collapse").sideNav();

	$("#addLocationp").click(function(){
		$("#newLocationp").openModal();
	});

	$("input, select, textarea").focus(function(){
		$("#newLocationpError").hide(0);
		$("#editLocationpError").hide(0);
	});

	$(".deleteLctp").click(function(){
		deleteId = $(this).attr("item-id");
		$("#deleteModal").openModal();
	});

	$("#confirmDeleteLocationp").click(function(){
		$("#deleteLctpForm"+deleteId).submit();
	});

	$(".btnEditLctp").click(function(){
		editId = $(this).attr("edit-id");
		$("#editlocationpid").val(editId);
		$("#nombreedit").val($("#editName"+editId).html());
		$("#direccionedit").val($("#editDirection"+editId).html());
		$("#urledit").val($("#editUrl"+editId).html());
		/*$("#telefonoedit").val($("#editPhone"+editId).html());
		$("#latitudedit").val($("#editLat"+editId).html());
		$("#longitudedit").val($("#editLong"+editId).html());*/
		$("#editLocationp").openModal();
	});

	$("#btnGuardarLocationpEdit").click(function(){
	    var error = 0;
	    if($("#nombreedit").val().length<1){
	      error++;
	    }
	    if($("#direccionedit").val().length<1){
	      error++;
	    }
	    if( $("#imagenedit").val().length > 0 ){
	      if( ($("#imagenedit").val().split('.').pop() !== "jpg" && $("#imagenedit").val().split('.').pop() !== "jpeg" && $("#imagenedit").val().split('.').pop() !== "JPG" && $("#imagenedit").val().split('.').pop() !== "JPEG") ) {
	        error++;
	      }
	    }
	    if($("#urledit").val().length<1){
	      error++;
	    }
	    /*
	    if($("#telefonoedit").val().length<1 || isNaN($("#telefonoedit").val()) ){
	      error++;
	    }
	    if($("#latitudedit").val().length<1 || isNaN($("#latitudedit").val()) ){
          error++;
        }
        if($("#longitudedit").val().length<1 || isNaN($("#longitudedit").val()) ){
          error++;
        }
        */

	    if(error===0){
	      $("#editLocationpForm").submit();
	    } else {
	      $("#editLocationpError").show();
	    }
	});

	$("#btnGuardarLocationp").click(function(){
		var error = 0;
        if($("#nombre").val().length<1){
          error++;
        }
        if($("#direccion").val().length<1){
          error++;
        }
        if($("#imagen").val().split('.').pop() !== "jpg" && $("#imagen").val().split('.').pop() !== "jpeg" && $("#imagen").val().split('.').pop() !== "JPG" && $("#imagen").val().split('.').pop() !== "JPEG") {
          error++;
        }
        if( $("#url").val().length<1 ){
          error++;
        }
        /*
        if($("#telefono").val().length<1 || isNaN($("#telefono").val()) ){
          error++;
        }
        if($("#latitud").val().length<1 || isNaN($("#latitud").val()) ){
          error++;
        }
        if($("#longitud").val().length<1 || isNaN($("#longitud").val()) ){
          error++;
        }
        */
        if(error===0){
          $("#newLocationpForm").submit();
        } else {
          $("#newLocationpError").show();
        }
	});

});
function showMessage(title, msg){
	$("#modalTitle").html(title);
	$("#modalMsg").html(msg);
	$("#globalModal").openModal();
}