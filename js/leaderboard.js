$(document).ready(function(){

    // Allows users to select a subject to view top 5 leaderboard.
    $("#lbSubjectSelect").change(function(){
        const subjectUUID = $(this).val();
        if(subjectUUID === ""){
            $("#leaderboardResults").html(`
                <p class="text-muted text-center">
                    Select a subject to view leaderboard.
                </p>
            `);
            return;
        }

        // AJAX to gather data from database.
        $.ajax({
            url: "./getLeaderboard",
            type: "POST",
            data: {subjectUUID: subjectUUID},
            dataType: "json",

            success: function(res){

                if(res.status == "success"){

                    // Form HTML using bootstrap.
                    let html = `<div class=" py-5">
                                    <h2 class="mb-4">Top 5 Scores</h2>
                                    <table class="table table-striped table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>#</th>
                                                <th>First Name</th>
                                                <th>Last Name</th>
                                                <th>Score</th>
                                            </tr>
                                        </thead>
                                        <tbody>`;
                    res.data.forEach(function(student, index)
                    {
                        let medal = "";

                        // Top 3 are colour coded.
                        if(index == 0) medal = '<span class="lb-gold">1</span>';
                        else if(index == 1) medal = '<span class="lb-silver">2</span>';
                        else if(index == 2) medal = '<span class="lb-bronze">3</span>';
                        else medal = index + 1;
                        
                        // All data is sanistised using htmlspecialchar in getLeaderboard.php.
                        html += `
                                    <tr>
                                        <td><strong>${medal}</strong></td>
                                        <td>${student.firstName}</td>
                                        <td>${student.lastName}</td>
                                        <td><span>${student.totalScore}</span></td>
                                    </tr>

                        `;
                    });
                    html+= `    </tbody>
                            </table>
                        </div>`;
                    $("#leaderboardResults").html(html);
                }

                else{
                    $("#leaderboardResults").html(`
                        <p class="text-danger text-center">
                            ${res.message}
                        </p>
                    `);
                }
            },

            // If error, display message.
            error:function( err){
                $("#leaderboardResults").html(`
                    <p class="text-danger text-center">
                        Failed to load leaderboard.
                    </p>
                `);
            }

        });

    });

});
