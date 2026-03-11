$(document).ready(function() {
    // Event listener for when the reset form is submitted.
    $("#reset-form").submit(function(event) {
        event.preventDefault(); 

        // Collect form data, getting token from URL.
        const token = new URLSearchParams(window.location.search).get("token"); 
        const password = $("#password").val().trim();
        const confirmPassword = $("#confirmPassword").val().trim();

        // Client-side validation.
        if (!password || !confirmPassword) {
            Swal.fire('Error', 'Please fill in all fields.', 'error');
            return;
        }

        if (password !== confirmPassword) {
            Swal.fire('Error', 'Passwords do not match.', 'error');
            return;
        }

        // Disable button to prevent double submission.
        const $btn = $("#reset-form button[type='submit']");
        $btn.prop("disabled", true);

        // Send AJAX request to server.
        $.ajax({
            url: "../updatePassword",
            type: "POST",
            data: { token: token, txtPassword: password, txtConfirmPassword: password },
            dataType: "json",
            success: function(res) {
                if (res.status === "success") {
                    Swal.fire({
                        title: 'Success',
                        text: res.message,
                        icon: 'success',
                        confirmButtonText: 'Go to Login'
                    }).then(() => {
                        // Full page reload to load the login page completely, ensures CSS/JS/images work correctly and URL updates.
                        window.location.href = "../login";
                    });
                } else {
                    // Send error message and reenable button.
                    Swal.fire('Error', res.message, 'error');
                    $btn.prop("disabled", false);
                }
            },
            error: function(xhr, status, error) {
                // Send error message and reenable button.
                Swal.fire('Error', 'An unexpected error occurred. Please try again.', 'error');
                $btn.prop("disabled", false);
            }
        });
    });

    // Event listener for "Back to login page" link.
    $(document).on("click", "#loginLink", function(e) {
        e.preventDefault(); 

        $.ajax({
            url: "../login",
            type: 'GET',
            success: function(data) {
                // Full page reload to load the login page completely, ensures CSS/JS/images work correctly and URL updates.
                window.location.href = "../login";
            },
            error: function() {
                Swal.fire('Error', 'Failed to load login page. Please try again.', 'error');
            }
        });
    });
});