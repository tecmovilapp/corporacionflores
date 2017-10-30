$(document).ready(function(){
  var idEdit = -1;
  var idDCar = -1;
  var idECar = -1;
  $(".button-collapse").sideNav();
  $('.tooltipped').tooltip({delay: 50});
  $('select').material_select();

  $("#addCat").click(function(){
    $("#newCat").openModal();
  });

  $("#btnGuardarCat").click(function(){
    var error = 0;
    if($("#nombre").val().length<1){
      error++;
    }

    if(error>0){
      $("#newCatError").show(0)
    } else {
      $("#newCatForm").submit();
    }
  });

  $("input").focus(function(){
    $("#newCatError").hide(0);
    $("#newCarError").hide(0);
  });

  $(".editCategory").click(function(){
    idEdit = $(this).attr("item-id");
    $("#editcatid").val(idEdit);
    $("#nombreEdit").val( $("#nombre"+idEdit).val() );
    $("#editCat").openModal();
  });

  $("#btnGuardarEditCat").click(function(){
    var error = 0;
    if($("#nombreEdit").val().length<1){
      error++;
    }

    if(error>0){
      $("#editCatError").show(0)
    } else {
      $("#editCatForm").submit();
    }
  });

  //INICIO DE FUNCIONES VEHICULOS

  $("#catSelect").change(function(){
    getCarsByCat();
  });

  $("#addCar").click(function(){
    $("#newCar").openModal();
  });

  $("#btnGuardarCar").click(function(){
    var error = 0;
    if($("#nombrec").val().length<1){
      error++;
    }

    if($("#descripcionc").val().length<1){
      error++;
    }

    if($("#imagenc").val().split('.').pop() !== "jpg" && $("#imagenc").val().split('.').pop() !== "jpeg" && $("#imagenc").val().split('.').pop() !== "JPG" && $("#imagenc").val().split('.').pop() !== "JPEG") {
      error++;
    }

    if($("#yearc").val() === null){
      error++;
    }

    if($("#categoriac").val() === null){
      error++;
    }

    if($("#pdfc").val().length<1){
      error++;
    }

    if(error>0){
      $("#newCarError").show(0)
    } else {
      $("#newCarForm").submit();
    }
  });

  $("#confirmDeleteCar").click(function(){
    deleteCar();
  });

  $("#btnGuardarEditCar").click(function(){
    var error = 0;
    if($("#nombrecedit").val().length<1){
      error++;
    }

    if($("#descripcioncedit").val().length<1){
      error++;
    }

    if($("#yearcedit").val() === null){
      error++;
    }

    if($("#categoriacedit").val() === null){
      error++;
    }

    if($("#pdfcedit").val().length<1){
      error++;
    }

    if(error>0){
      $("#editCarError").show(0)
    } else {
      $("#editCarForm").submit();
    }
  });

});

function goGallery(obj){
  $("#formGoGallery"+$(obj).attr("item-id")).submit();
}

function showMessage(title, msg){
  $("#modalTitle").html(title);
  $("#modalMsg").html(msg);
  $("#globalModal").openModal();
}

function confirmDeleteCar(obj){
  idDCar = $(obj).attr("item-id");
  $("#deleteModalCar").openModal();
}

function editCar(obj){
  idECar= $(obj).attr("item-id");
  $("#editcarid").val(idECar);
  $("#nombrecedit").val($("#editnombre"+idECar).html());
  $("#pdfcedit").val($("#editPdf"+idECar).html());
  $("#descripcioncedit").val($("#editDescription"+idECar).html());
  $("#yearcedit").val($("#editYear"+idECar).html());
  $("#yearcedit").material_select();
  $("#categoriacedit").val($("#catSelect").val());
  $("#categoriacedit").material_select();
  $("#editModalCar").openModal();
}

function deleteCar(){
  var request = "delete-car&idcar="+idDCar;
  $.getJSON( host+request, function( data ) {
    showMessage("Aviso", "El vehiculo y su galer&iacute;a han sido eliminados.");
    getCarsByCat();
  }).error(function(e) {
    console.log(JSON.stringify(e));
    showMessage("Error", "Ocurrio un error al intentar eliminar el vehiculo.")
  });
}

function getCarsByCat(){
  $("#loading").show(0);
  var request = "get-cars-by-cat&idcat="+$("#catSelect").val();
  $.getJSON( host+request, function( data ) {
    $("#loading").hide(0);
    var contentCar = "";
    if(data.length>0){
      for(var i=0;i<data.length;i++){
        contentCar += '<li><div class="collapsible-header">'+data[i].year+' - '+data[i].nombre+'</div>';
        contentCar += '<div class="collapsible-body"><a class="waves-effect waves-light btn gflores btnEditCar" style="float:right;margin:10px;" item-id="'+data[i].id+'" onclick="editCar(this)">Editar</a><a class="waves-effect waves-light btn gflores" style="float:right;margin:10px;" item-id="'+data[i].id+'" onclick="goGallery(this)">Galer&iacute;a</a><a class="waves-effect waves-light btn gflores" onclick="confirmDeleteCar(this)" style="float:right;margin:10px;" item-id="'+data[i].id+'">Eliminar</a><img src="images/cars/'+data[i].foto+'" style="width:auto;margin:auto;margin-top:0px;width:50%;margin-left:25%;"/><p>'+data[i].descripcion+'<br><br>PDF: '+data[i].pdf+'</p></div></li>';
        contentCar += '<div style="display:none;"><span id="editnombre'+data[i].id+'">'+data[i].nombre+'</span><span id="editPdf'+data[i].id+'">'+data[i].pdf+'</span><span id="editDescription'+data[i].id+'">'+data[i].descripcion+'</span><span id="editYear'+data[i].id+'">'+data[i].year+'</span></div>';
        contentCar += '<div style="display:none"><form action="vehiculo_galeria.php" method="GET" id="formGoGallery'+data[i].id+'"><input type="hidden" name="idcar" value="'+data[i].id+'"><input type="hidden" name="idcat" value="'+$("#catSelect").val()+'"></form></div>';
      }
      $("#carslist").html(contentCar);
    } else {
      $("#carslist").html("");
      Materialize.toast('No existen vehiculos en esta categoria.', 4000);
    }
  }).error(function(e) {
    $("#loading").hide(0);
  });
}//FIN