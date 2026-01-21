// Event listener for when login form is submitted.
$("#login-form").submit(function (event) 
{
    // prevents default form submission behaviour.
    event.preventDefault();

    // Clears session and local storage for security reasons.
    sessionStorage.clear();
    localStorage.clear(); 

    console.log("Before Ajax")

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
                        Swal.fire("Welcome to the dashboard!");
                    },
                    error: function() {
                        // Display error.
                        alert("Error loading the dashboard. Please try again.");
                    }
                });
            } 
            else 
            {
                // Display SweetAlert error popup.
                Swal.fire({
                title: 'Login Failed',
                text: 'Your username or password is incorrect. Please try again.',
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