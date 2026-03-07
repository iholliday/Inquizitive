// Event listener for when login form is submitted.
$("#enable-mfa").click(function (event) 
{
    console.log('h');
    // AJAX for loading MFA generate php.
    $.ajax({
        url: "./mfaGenerate",
        type: "GET",
        dataType: "json",
        success: function(res){

            // If successful, load modal with QR code.
            if(res.status === "success"){

                Swal.fire({
                    title: "Scan QR Code",
                    html:
                        '<p>Scan this with an authenticator app such as Google Authenticator</p>' +
                        '<img src="'+res.qr+'" style="width:200px;">' +
                        '<p>Next, enter the 6 digit code below:</p>',
                    input: "text",
                    confirmButtonText: "Verify"
                }).then(function(result){

                    var code = result.value;

                    // Confirm addition of MFA via AJAX.
                    $.ajax({
                        url: "./mfaConfirm",
                        type: "POST",
                        data: {code: code},
                        dataType: "json",
                        success: function(r){
                            Swal.fire(r.message);
                        }
                    });

                });

            }

        }
    });
});