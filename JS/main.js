function showForm(formId) {
    document.querySelectorAll(".form-box").forEach(form => form.classList.remove("active"));
    document.getElementById(formId).classList.add("active");
}

document.getElementById("frmRegistro").addEventListener("submit", function(e){

    e.preventDefault();

    let datos = new FormData(this);

    fetch("servicio.php?btn_registrar",{
        method:"POST",
        body:datos
    })
    .then(res=>res.json())
    .then(data=>{

        if(data.status==="ok"){


            document.getElementById("frmRegistro").reset();

            showForm("login-form"); // volver al login

        }
        else{
            errorRegistro.innerText = data.message;
        }

    });

});