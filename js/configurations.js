var deleteId = -1;
var deleteIdW = -1;
var deleteIdM = -1

$(document).ready(function(){
	$(".button-collapse").sideNav();
  getInfoAppointment();
  getInfoQuote();
  getInfoContact();
  getInfoBAC();
  getInfoAvance();
  getTypes();
  getModels();

  var toolbar = [
      ['color', ['color']],
      ['style', ['bold', 'underline', 'clear']],
      ['fonts', ['fontsize']],
      ['para', ['ul', 'leftButton', 'centerButton', 'rightButton', 'justifyButton']],
      ['misc', ['codeview']],
  ];

  $('.editor').materialnote({
      toolbar: toolbar,
      height: 550,
      minHeight: 100,
      defaultBackColor: '#e0e0e0'
  });

  $("#confirmDeleteType").click(function(){
    var request = "delete-type&idtype="+deleteId;
    $.getJSON( host+request, function( data ) {
      if(data.result===1){
        Materialize.toast('El registro ha sido eliminado.', 4000);
        getTypes();
      }
    }).error(function(e) {
      showMessage("Error", "Ocurrio un error al eliminar el registro.");
    });
  });

  $("#addType").click(function(){
    $("#newType").openModal();
  });

  $("input").focus(function(){
    $("#newTypeError").hide(0);
    $("#newWorkshopError").hide(0);
  });

  $("#btnGuardarType").click(function(){
    if($("#nombret").val().length<1){
      $("#newTypeError").show(0);
      return;
    }
    var request = "create-type&nombret="+$("#nombret").val();
    $.getJSON( host+request, function( data ) {
      if(data.result===1){
        Materialize.toast('El registro ha sido creado.', 4000);
        $("#newType").closeModal();
        $("#nombret").val("");
        getTypes();
      }
    }).error(function(e) {
      showMessage("Error", "Ocurrio un error al crear el registro.");
    });
  });

  $("#addWorkshop").click(function(){
    $("#newWorkshop").openModal();
  });

  $("#confirmDeleteWorkshop").click(function(){
    var request = "delete-workshop&idworkshop="+deleteIdW;
    $.getJSON( host+request, function( data ) {
      if(data.result===1){
        Materialize.toast('El registro ha sido eliminado.', 4000);
        getWorkshops();
      }
    }).error(function(e) {
      showMessage("Error", "Ocurrio un error al eliminar el registro.");
    });
  });

  $("#btnGuardarWorkshop").click(function(){
    if($("#nombrew").val().length<1){
      $("#newWorkshopError").show(0);
      return;
    }

    var request = "create-workshop&nombrew="+$("#nombrew").val();
    $.getJSON( host+request, function( data ) {
      if(data.result===1){
        Materialize.toast('El registro ha sido creado.', 4000);
        $("#newWorkshop").closeModal();
        $("#nombrew").val("");
        getWorkshops();
      }
    }).error(function(e) {
      showMessage("Error", "Ocurrio un error al crear el registro.");
    });

  });//FIN btnGuardarWorkshop

  $("#addModel").click(function(){
    $("#newModel").openModal();
  });

  $("#confirmDeleteModel").click(function(){
    console.log(deleteIdM);
    var request = "delete-model&idmodel="+deleteIdM;
    console.log(host+request);
    $.getJSON( host+request, function( data ) {
      if(data.result===1){
        Materialize.toast('El registro ha sido eliminado.', 4000);
        getModels();
      }
    }).error(function(e) {
      showMessage("Error", "Ocurrio un error al eliminar el registro.");
    });
  });

  $("#btnGuardarModel").click(function(){
    if($("#nombrem").val().length<1){
      $("#newModelError").show(0);
      return;
    }

    var request = "create-model&nombrem="+$("#nombrem").val();

    $.getJSON( host+request, function( data ) {
      if(data.result===1){
        Materialize.toast('El registro ha sido creado.', 4000);
        $("#newModel").closeModal();
        $("#nombrem").val("");
        getModels();
      }
    }).error(function(e) {
      showMessage("Error", "Ocurrio un error al crear el registro.");
    });

  });//FIN 

});//FIN DOCUMENT READY

function openDeleteModel(id){
  deleteIdM = id;
  $("#deleteModalModel").openModal();
}

function getModels(){
  var request = "get-models";
  var listModels = "";
  $.getJSON( host+request, function( data ) {
    for(var i=0;i<data.length;i++) {
      listModels += '<li class="collection-item"><div style="min-height:40px;">'+data[i].nombre+'<div class="secondary-content tooltipped" data-tooltip="Eliminar" data-position="left" style="cursor:pointer;" onclick="openDeleteModel(\''+data[i].nombre+'\')"><i class="material-icons gflores-text" style="font-size:40px;">delete</i></div></div><input type="hidden" id="nombre'+data[i].model+'" value="'+data[i].nombre+'"></li>';
    }
    $("#listModels").html(listModels);
    $(".tooltipped").tooltip();
  }).error(function(e) {
    console.log(JSON.stringify(e));
    showMessage("Error", "Ocurrio un error al recuperar la informaci&oacute;n.");
  });
}

function openDeleteWorkshop(id){
  deleteIdW = id;
  $("#deleteModalWorkshop").openModal();
}

function getWorkshops(){
  var request = "get-workshops";
  var listWorkshops = "";
  $.getJSON( host+request, function( data ) {
    for(var i=0;i<data.length;i++) {
      listWorkshops += '<li class="collection-item"><div style="min-height:40px;">'+data[i].nombre+'<div class="secondary-content tooltipped" data-tooltip="Eliminar" data-position="left" style="cursor:pointer;" onclick="openDeleteWorkshop('+data[i].id+')"><i class="material-icons gflores-text" style="font-size:40px;">delete</i></div></div><input type="hidden" id="nombre'+data[i].id+'" value="'+data[i].nombre+'"></li>';
    }
    $("#listWorkshops").html(listWorkshops);
    $(".tooltipped").tooltip();
  }).error(function(e) {
    console.log(JSON.stringify(e));
    showMessage("Error", "Ocurrio un error al recuperar la informaci&oacute;n.");
  });
}

function openDeleteType(id){
  deleteId = id;
  $("#deleteModalType").openModal();
}

function getTypes(){
  var request = "get-types";
  var listTypes = "";
  $.getJSON( host+request, function( data ) {
    for(var i=0;i<data.length;i++) {
      listTypes += '<li class="collection-item"><div style="min-height:40px;">'+data[i].nombre+'<div class="secondary-content tooltipped" data-tooltip="Eliminar" data-position="left" style="cursor:pointer;" onclick="openDeleteType('+data[i].id+')"><i class="material-icons gflores-text" style="font-size:40px;">delete</i></div></div><input type="hidden" id="nombre'+data[i].id+'" value="'+data[i].nombre+'"></li>';
    }
    $("#listTypes").html(listTypes);
    $(".tooltipped").tooltip();
  }).error(function(e) {
    console.log(JSON.stringify(e));
    showMessage("Error", "Ocurrio un error al recuperar la informaci&oacute;n.");
  });
}

function getInfoAvance(){
  var request = "get-avance-info";
  $.getJSON( host+request, function( data ) {
    $("#emailavance").val(data.correo);
    $("#editoravance").code(data.descripcion)
    $("#pdfavance").val(data.pdf);
    $("#videoavance").val(data.video);
  }).error(function(e) {
    console.log(JSON.stringify(e));
    showMessage("Error", "Ocurrio un error al recuperar la informaci&oacute;n.");
  });
}

function updateInfoAvance(){
  var email = $("#emailavance").val();
  var contenido = $("#editoravance").code();
  var pdf = $("#pdfavance").val();
  var video = $("#videoavance").val();
  var error = 0;
  if( !validateEmail(email) ){
    error++;
  }
  if(contenido.length<1){
    error++;
  }
  if(pdf.length<1){
    error++;
  }
  if(video.length<1){
    error++;
  }
  if(error>0){
    showMessage("Error","El formulario contiene uno o mas errores.");
    return;
  }
  var request = "update-avance-info&emailavance="+email+"&contenido="+contenido.toString()+"&pdf="+pdf+"&video="+video;
  $.getJSON( host+request, function( data ) {
    if(data.result===1){
      Materialize.toast('La informaci&oacute;n de contacto de Avance Plus ha sido actualizada.', 4000);
    }
  }).error(function(e) {
    console.log(JSON.stringify(e));
    showMessage("Error", "Ocurrio un error al actualizar la informaci&oacute;n.")
  });
}

function getInfoBAC(){
  var request = "get-bac-info";
  $.getJSON( host+request, function( data ) {
    $("#emailbac").val(data.correo);
    $("#editorbac").code(data.descripcion)
    $("#pdfbac").val(data.pdf);
  }).error(function(e) {
    console.log(JSON.stringify(e));
    showMessage("Error", "Ocurrio un error al recuperar la informaci&oacute;n.");
  });
}

function updateInfoBAC(){
  var email = $("#emailbac").val();
  var contenido = $("#editorbac").code();
  var pdf = $("#pdfbac").val();
  var error = 0;
  if( !validateEmail(email) ){
    error++;
  }
  if(contenido.length<1){
    error++;
  }
  if(pdf.length<1){
    error++;
  }
  if(error>0){
    showMessage("Error","El formulario contiene uno o mas errores.");
    return;
  }
  var request = "update-bac-info&emailbac="+email+"&contenido="+contenido.toString()+"&pdf="+pdf;
  $.getJSON( host+request, function( data ) {
    if(data.result===1){
      Materialize.toast('La informaci&oacute;n de las tarjetas BAC ha sido actualizada.', 4000);
    }
  }).error(function(e) {
    console.log(JSON.stringify(e));
    showMessage("Error", "Ocurrio un error al actualizar la informaci&oacute;n.")
  });
}

function getInfoAppointment(){
  var request = "get-appointment-info";
  $.getJSON( host+request, function( data ) {
    $("#emailappointment").val(data.correo);
  }).error(function(e) {
    console.log(JSON.stringify(e));
    showMessage("Error", "Ocurrio un error al recuperar la informaci&oacute;n.");
  });
}

function updateInfoAppointment(){
  var email = $("#emailappointment").val();
  if( !validateEmail(email) ){
    showMessage("Error", "Por favor ingresa una direccion de correo electronico valido");
    return;
  }
  var request = "update-appointment-info&emailap="+email;
  $.getJSON( host+request, function( data ) {
    if(data.result===1){
      Materialize.toast('La informaci&oacute;n para citas de servicios ha sido actualizada.', 4000);
    }
  }).error(function(e) {
    console.log(JSON.stringify(e));
    showMessage("Error", "Ocurrio un error al actualizar la informaci&oacute;n.")
  });
}

function getInfoQuote(){
  var request = "get-quote-info";
  $.getJSON( host+request, function( data ) {
    $("#emailquote").val(data.correo);
  }).error(function(e) {
    console.log(JSON.stringify(e));
    showMessage("Error", "Ocurrio un error al recuperar la informaci&oacute;n.");
  });
}

function updateInfoQuote(){
  var email = $("#emailquote").val();
  if( !validateEmail(email) ){
    showMessage("Error", "Por favor ingresa una direccion de correo electronico valido");
    return;
  }
  var request = "update-quote-info&emailq="+email;
  $.getJSON( host+request, function( data ) {
    if(data.result===1){
      Materialize.toast('La informaci&oacute;n para cotizaciones ha sido actualizada.', 4000);
    }
  }).error(function(e) {
    console.log(JSON.stringify(e));
    showMessage("Error", "Ocurrio un error al actualizar la informaci&oacute;n.")
  });
}

function getInfoContact(){
  var request = "get-contact-info";
  $.getJSON( host+request, function( data ) {
    $("#emailcontact").val(data.correo);
    $("#phonecontact").val(data.telefono);
    $("#chatcontact").val(data.enlace);
  }).error(function(e) {
    console.log(JSON.stringify(e));
    showMessage("Error", "Ocurrio un error al recuperar la informaci&oacute;n.");
  });
}

function updateInfoContact(){
  var email = $("#emailcontact").val();
  var telefono = $("#phonecontact").val();
  var link = $("#chatcontact").val();
  var error = 0;
  if( !validateEmail(email) ){
    error++;
  }
  if(telefono.length<1 || isNaN(telefono)){
    error++;
  }
  if(link.length<1){
    error++;
  }
  if(error>0){
    showMessage("Error","El formulario contiene uno o mas errores.");
    return;
  }
  var request = "update-contact-info&emailc="+email+"&phone="+telefono+"&url="+link;
  $.getJSON( host+request, function( data ) {
    if(data.result===1){
      Materialize.toast('La informaci&oacute;n de contacto ha sido actualizada.', 4000);
    }
  }).error(function(e) {
    console.log(JSON.stringify(e));
    showMessage("Error", "Ocurrio un error al actualizar la informaci&oacute;n.")
  });
}

function showMessage(title, msg){
	$("#modalTitle").html(title);
	$("#modalMsg").html(msg);
	$("#globalModal").openModal();
}

function validateEmail(email) {
    var re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}