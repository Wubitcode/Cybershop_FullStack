// assets/js/app.js

document.addEventListener('DOMContentLoaded', function() {
    console.log("Cyber_System: Online");

    // Example: Add a confirmation to all delete buttons
    const deleteButtons = document.querySelectorAll('.text-danger');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if(!confirm("ARE_YOU_SURE? THIS_ACTION_IS_IRREVERSIBLE.")) {
                e.preventDefault();
            }
        });
    });
});