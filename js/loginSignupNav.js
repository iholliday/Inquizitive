$(document).ready(function()
{
    // Ajax for loading full pages prior to signin.
    function loadPage(page)
    {
        $.ajax({
            url: "./" + page, 
            type: 'GET',
            success: function(data) {
                // If successful, load html.
                $('body').html(data);
                history.pushState({ page: page }, "", page); 
            },
            error: function() {
                // Else display error.
                alert("Error loading page. Please try again.");
            }
        });
    }

    // Modal popup asking for email to send passowrd reset token.
    $(document).on("click", "#forgotPassword", function(e) {
        e.preventDefault();
        console.log("Forgot password clicked!");

        Swal.fire({
            title: 'Reset Password',
            text: 'Enter your email to receive a password reset link:',
            input: 'email',
            inputAttributes: { autocapitalize: 'none', maxlength: 255, placeholder: 'Enter your email' },
            showCancelButton: true,
            confirmButtonText: 'Send Reset Link',
            cancelButtonText: 'Cancel',
            inputValidator: (email) => {
                email = email.trim();
                if (!email) return 'Please enter your email.';
                // Email format validation.
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(email)) return 'Please enter a valid email address.';
            }
        }).then(function(result) {
            if (result.isConfirmed) {
                var email = result.value.trim();
                $.ajax({
                    url: "./sendPasswordReset",
                    type: "POST",
                    data: { email: email },
                    dataType: "json",
                    success: function(res) {
                        if (res.status === 'success') {
                            Swal.fire('Success', res.message || 'If that account exists, a reset link has been sent.', 'success');
                        } else {
                            Swal.fire('Error', res.message || 'Failed to send reset link.', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log("Raw response:", xhr.responseText);
                        Swal.fire('Error', 'An error occurred while sending the reset link.', 'error');
                    }
                });
            }
        });
    });

    // Navigation between login pages.
    $("#loginLink").click(function(e){
        e.preventDefault();
        loadPage("login");
    });

    $("#signupLink").click(function(e){
        e.preventDefault();
        loadPage("signup");
    });

    $("#dashboardLink").click(function(e){
        e.preventDefault();
        loadPage("dashboard");
    });
});

