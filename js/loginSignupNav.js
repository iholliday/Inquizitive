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
            },
            error: function() {
                // Else display error.
                alert("Error loading dashboard. Please try again.");
            }
        });
    }

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

