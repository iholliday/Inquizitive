// Password indicator to see if new password meets requirements before subission.
function setupPasswordIndicator(passwordSelector, indicatorSelector) {
    const passwordInput = document.querySelector(passwordSelector);
    const indicator = document.querySelector(indicatorSelector);

    if (!passwordInput || !indicator) return;

    // Checks to see if password meets rules, testing each character and length.
    passwordInput.addEventListener("input", function () {
        const val = passwordInput.value;

        const minLength = val.length >= 8;
        const uppercase = /[A-Z]/.test(val);
        const lowercase = /[a-z]/.test(val);
        const number = /[0-9]/.test(val);
        const special = /[^\w]/.test(val);

        const valid = minLength && uppercase && lowercase && number && special;

        // Empty password shows as grey, illegal red, and legal green.
        if (val.length === 0) {
            indicator.style.backgroundColor = "#9c9c9c";
        } else if (valid) {
            indicator.style.backgroundColor = "#79df60";
        } else {
            indicator.style.backgroundColor = "rgb(247, 85, 85)";
        }
    });
}

// Call function for password indicator.
setupPasswordIndicator("#password", "#password-indicator");

// Event listener for create account form.
$('#signup-form').submit(function (e) 
{
    // Prevents default behaviour.
    e.preventDefault();

    // To prevent users from submitting blank data by avoiding the "required" with the use of white space.
    let nullValue = false;
    let inputData = [
        $('#firstName').val().trim(),
        $('#lastName').val().trim(),
        $('#email').val().trim(),
        $('#password').val().trim(),
        $('#passwordConfirm').val().trim()
    ];

    // Alert user if password and confirmation password do not match.
    if ($('#password').val().trim() !== $('#passwordConfirm').val().trim())
    {
        Swal.fire("Error!", "Please ensure passwords match!", "error");
        return;
    }

    // Checking for empty entries.
    for (let iCount = 0; iCount < inputData.length; iCount++) 
    {
        if (inputData[iCount] == "")
        {
            nullValue = true;
            break;
        }
    }
    
    // If any are left blank, alert user with an error message. Else, insert data.
    if (nullValue == true)
    {
        Swal.fire("Error!", "Please ensure no fields are left blank!", "error");
    }
    else 
    {
        // AJAX request to create account
        $.ajax({
            url: "./createAccount",     
            type: "POST",
            data: $('#signup-form').serialize(),
            dataType: "json",
            success: function(res) {
                console.log("AJAX Success - signup:", res);

                if (res.status === "success") {
                    Swal.fire("Account created!", res.message, "success").then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire("Error!", res.message, "error");
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error - signup:", status, error, xhr.responseText);
                Swal.fire("Error!", "An error occurred while creating your account. Please try again.", "error");
            }
        });
    }
});