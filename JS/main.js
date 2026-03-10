function showForm(formId) {
    document.querySelectorAll(".form-box").forEach(form => form.classList.remove("active"));
    document.getElementById(formId).classList.add("active");
}

$("#frmRegistro").submit(function(e) {

    e.preventDefault();

    $.post("servicio.php?btn_registrar", $(this).serialize(), function(data){

        let errorRegistro = document.getElementById("errorRegistro");

        if(data.status === "ok"){

            errorRegistro.innerText = "";
            document.getElementById("frmRegistro").reset();
            showForm("login-form");

        }
        else{

            errorRegistro.innerText = data.message;

        }

    }, "json");

});



