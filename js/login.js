// Event listener for when login form is submitted.
$("#login-form").submit(function (event) 
{
    // prevents default form submission behaviour.
    event.preventDefault();

    // Clears session and local storage for security reasons.
    sessionStorage.clear();
    localStorage.clear(); 

    // AJAX request to authenticate login via POST, ensuring that the server response is in JSON.
    $.ajax({
        url: "./auth",
        type: "POST",
        data: $('#login-form').serialize(),
        dataType: "json",
        success: function(res) {
            // Log success response.
            console.log("AJAX Success:", res);
            
            if (res.status === 'success') 
            {
                // If auth is successful, redirect user to dashboard page via AJAX.
                $.ajax({
                    url: "./dashboard", 
                    type: 'GET',
                    success: function(data) {
                        // Replace the content of the body with the dashboard.
                        $('body').html(data); 
                        Swal.fire(res.message);
                    },
                    error: function() {
                        // Display error.
                        alert("Error loading the dashboard. Please try again.");
                    }
                });
            }
            else if (res.status === 'mfaEnabled') {
                // If MFA is enabled, prompt user for TOTP/backup code.
                Swal.fire({
                    title: 'Multi-Factor Authentication',
                    text: 'Enter your 6-digit TOTP code or 16-character backup code:',
                    input: 'text',
                    inputAttributes: { autocapitalize: 'characters', maxlength: 16 },
                    showCancelButton: true,
                    confirmButtonText: 'Verify',
                    cancelButtonText: 'Cancel',
                    inputValidator: (code) => {
                        code = code.trim();
                        if (!code) return 'Please enter a code.';
                        if (code.length !== 6 && code.length !== 16) return 'Code must be 6 or 16 characters long.';
                    }
                }).then(function(result) {
                    if (result.isConfirmed) {
                        var code = result.value.trim();
                        $.ajax({
                            url: "./mfaEnabledAuth",
                            type: "POST",
                            data: { code: code },
                            dataType: "json",
                            success: function(mfaRes) {
                                if (mfaRes.status === 'success') {
                                    console.log("helllo");
                                    $.ajax({
                                        url: "./dashboard",
                                        type: 'GET',
                                        success: function(data) {
                                            $('body').html(data);
                                            Swal.fire(mfaRes.message);
                                        },
                                        error: function() { alert("Error loading dashboard."); }
                                    });
                                } else {
                                    Swal.fire('MFA Failed', mfaRes.message, 'error');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error("MFA AJAX Error:", status, error);
                                console.log("Raw response:", xhr.responseText);
                                alert("An error occurred during MFA verification.");
                            }
                        });
                    }
                });
            }
            else 
            {
                // Display SweetAlert error popup.
                Swal.fire({
                title: 'Login Failed',
                text: res.message,
                icon: 'error',
                confirmButtonText: 'OK'
            });
            }
        },
        error: function(xhr, status, error) {
            // Display error.
            console.error("AJAX Error:", status, error);
            alert("An error occurred. Please try again.");
        }
        });
    });