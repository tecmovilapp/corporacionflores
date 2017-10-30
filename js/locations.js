$(document).ready(function(){
	var editId = -1;
	var deleteId = -1;

	$(".button-collapse").sideNav();

	$("#addLocation").click(function(){
		$("#newLocation").openModal();
	});

	$("input, select, textarea").focus(function(){
		$("#newLocationError").hide(0);
		$("#editLocationError").hide(0);
	});

	$(".deleteLct").click(function(){
		deleteId = $(this).attr("item-id");
		$("#deleteModal").openModal();
	});

	$("#confirmDeleteLocation").click(function(){
		$("#deleteLctForm"+deleteId).submit();
	});

	$(".btnEditLct").click(function(){
		editId = $(this).attr("edit-id");
		$("#editlocationid").val(editId);
		$("#nombreedit").val($("#editName"+editId).html());
		$("#direccionedit").val($("#editDirection"+editId).html());
		$("#telefonoedit").val($("#editPhone"+editId).html());
		$("#latitudedit").val($("#editLat"+editId).html());
		$("#longitudedit").val($("#editLong"+editId).html());
		$("#editLocation").openModal();
	});

	$("#btnGuardarLocationEdit").click(function(){
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
	    if($("#telefonoedit").val().length<1 || isNaN($("#telefonoedit").val()) ){
	      error++;
	    }
	    if($("#latitudedit").val().length<1 || isNaN($("#latitudedit").val()) ){
          error++;
        }
        if($("#longitudedit").val().length<1 || isNaN($("#longitudedit").val()) ){
          error++;
        }

	    if(error===0){
	      $("#editLocationForm").submit();
	    } else {
	      $("#editLocationError").show();
	    }
	});

	$("#btnGuardarLocation").click(function(){
		var error = 0;
        if($("#nombre").val().length<1){
          error++;
        }
        if($("#direccion").val().length<1){
          error++;
        }

        if($("#telefono").val().length<1 || isNaN($("#telefono").val()) ){
          error++;
        }

        if($("#imagen").val().split('.').pop() !== "jpg" && $("#imagen").val().split('.').pop() !== "jpeg" && $("#imagen").val().split('.').pop() !== "JPG" && $("#imagen").val().split('.').pop() !== "JPEG") {
          error++;
        }
        if($("#latitud").val().length<1 || isNaN($("#latitud").val()) ){
          error++;
        }
        if($("#longitud").val().length<1 || isNaN($("#longitud").val()) ){
          error++;
        }

        if(error===0){
          $("#newLocationForm").submit();
        } else {
          $("#newLocationError").show();
        }
	});

});
function showMessage(title, msg){
	$("#modalTitle").html(title);
	$("#modalMsg").html(msg);
	$("#globalModal").openModal();
}