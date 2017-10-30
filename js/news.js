$(document).ready(function(){
	var editId = -1;
	var deleteId = -1;
	$(".button-collapse").sideNav();
	$("#addNews").click(function(){
		$("#newNews").openModal();
	});

	$("input").focus(function(){
		$("#newNewsError").hide(0);
	});

	$(".deletePromo").click(function(){
		deleteId = $(this).attr("item-id");
		$("#deleteModal").openModal();
	});

	$("#confirmDeleteNews").click(function(){
		$("#deleteNewsForm"+deleteId).submit();
	});

	$(".btnEditNews").click(function(){
		editId = $(this).attr("edit-id");
        $("#editnewsid").val(editId);
        $("#tituloedit").val($("#editTitulo"+editId).html());
        $("#enlaceedit").val($("#editEnlace"+editId).html());
        $("#contenidoedit").val($("#editContenido"+editId).html());
		$("#editNews").openModal();
	});

	$("#btnGuardarNewsEdit").click(function(){
	    var error = 0;
	    if($("#tituloedit").val().length<1){
	      error++;
	    }
	    if($("#enlaceedit").val().length<1){
	      error++;
	    }
	    if($("#contenidoedit").val().length<1){
	      error++;
	    }
	    if( $("#imagenedit").val().length > 0 ){
	      if( ($("#imagenedit").val().split('.').pop() !== "jpg" && $("#imagenedit").val().split('.').pop() !== "jpeg" && $("#imagenedit").val().split('.').pop() !== "JPG" && $("#imagenedit").val().split('.').pop() !== "JPEG") ) {
	        error++;
	      }
	    }

	    if(error===0){
	      $("#editNewsForm").submit();
	    } else {
	      $("#editNewsError").show();
	    }
	});

	$("#btnGuardarNews").click(function(){
		var error = 0;
        if($("#titulo").val().length<1){
          error++;
        }
        if($("#enlace").val().length<1){
          error++;
        }
        if($("#contenido").val().length<1){
          error++;
        }
        if($("#imagen").val().split('.').pop() !== "jpg" && $("#imagen").val().split('.').pop() !== "jpeg" && $("#imagen").val().split('.').pop() !== "JPG" && $("#imagen").val().split('.').pop() !== "JPEG") {
          error++;
        }

        if(error===0){
          $("#newNewsForm").submit();
        } else {
          $("#newNewsError").show();
        }
	});
});
function showMessage(title, msg){
	$("#modalTitle").html(title);
	$("#modalMsg").html(msg);
	$("#globalModal").openModal();
}