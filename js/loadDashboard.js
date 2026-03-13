// Ajax for loading dashboard.
$.ajax({
    url: "./dashboard", 
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