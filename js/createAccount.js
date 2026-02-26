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