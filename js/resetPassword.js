
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
            data: { token: token, txtPassword: password },
            dataType: "json",
            success: function(res) {
                if (res.status === "success") {
                    Swal.fire({
                        title: 'Success',
                        text: res.message,
                        icon: 'success',
                        confirmButtonText: 'Go to Login'
                    }).then(() => {
                        window.location.href = "./login";
                    });
                } else {
                    // Send error message and reenable button.
                    Swal.fire('Error', res.message, 'error');
                    $btn.prop("disabled", false);
                }
            },
            error: function(xhr, status, error) {
                // Send error message and reenable button.
                console.error("AJAX Error:", status, error);
                    console.log("AJAX raw response:", xhr.responseText);

                Swal.fire('Error', 'An unexpected error occurred. Please try again.', 'error');
                $btn.prop("disabled", false);
            }
        });
    });
});