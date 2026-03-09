$(document).ready(function(){
  
    // Generate MFA with an authenticator app.
    $("#enable-mfa").click(function (event) 
    {
        // Ask user for their password prior to creating MFA.
        Swal.fire({
            title: 'Verify Password',
            input: 'password',
            inputAttributes: { autocapitalize: 'off', placeholder: 'Please enter your current password' },
            showCancelButton: true,
            confirmButtonText: 'Continue',
            cancelButtonText: 'Cancel',
            inputValidator: (password) => {
                if (!password) return 'Please enter your password.';
            }
        }).then(function(result) {
            if (!result.isConfirmed) return;

            const password = result.value.trim();

            // AJAX for loading MFA generate php.
            $.ajax({
                url: "./mfaGenerate",
                type: "POST",
                data: { password: password },
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
                                },
                                error: function(xhr, status, error) {
                                    console.error("MFA Confirm AJAX Error:", status, error);
                                    Swal.fire('Error', 'An unexpected error occurred. Please try again.', 'error');
                                }
                            });

                        });

                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }

                },
                error: function(xhr, status, error) {
                    console.error("MFA Generate AJAX Error:", status, error);
                    Swal.fire('Error', 'An unexpected error occurred. Please try again.', 'error');
                }
            });

        });
    });

    // Create backup codes for users who lost MFA access.
    $("#generate-backup").click(function (event) 
    {
        event.preventDefault();

        // Ask user for their password prior to generating backup code.
        Swal.fire({
            title: 'Verify Password',
            input: 'password',
            inputAttributes: { autocapitalize: 'off', placeholder: 'Please enter your current password' },
            showCancelButton: true,
            confirmButtonText: 'Continue',
            cancelButtonText: 'Cancel',
            inputValidator: (password) => {
                if (!password) return 'Please enter your password.';
            }
        }).then(function(result) {
            if (!result.isConfirmed) return;

            const password = result.value.trim();

            // AJAX for loading backup code generate php.
            $.ajax({
                url: "./generateBackupCode",
                type: "POST",
                data: { password: password },
                dataType: "json",
                success: function(res){

                    // If successful, load modal with backup code.
                    if(res.status === "success"){

                        Swal.fire({
                            title: "Backup MFA Code",
                            html:
                                '<p>Save this backup code in a secure place</p>' +
                                '<p style= "margin-top: -14px; margin-bottom: 25px;">You can only use it once!</p>' +
                                '<h3 style="letter-spacing: 2px; margin-bottom: 0px; font-family: monospace; filter: blur(4px); cursor: pointer;" ' +
                                'title="Click to reveal" onclick="this.style.filter=\'none\'">' + res.backupCode + '</h3>',
                            confirmButtonText: "Done"
                        });
                    } else {
                        // Show error if something went wrong.
                        Swal.fire({
                            title: "Error",
                            text: res.message,
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                    Swal.fire({
                        title: "AJAX Error",
                        text: "An error occurred while generating the backup code. Please try again.",
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                }
            });

        });
    });
});