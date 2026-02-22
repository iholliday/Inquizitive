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

$(document).on("click", "#loginLink", function(e) {
    e.preventDefault();
    loadPage("login");
});

$(document).on("click", "#signupLink", function(e) {
    e.preventDefault();
    loadPage("signup");
});


