$(document).ready(function()
{
    $("#lbSubjectSelect").change(function(){
        const subjectUUID = $(this).val();

        if(subjectUUID === ""){
            $("#leaderboardResults .placeholder-text").show();
            $("#leaderboardResults .leaderboard-table").hide();
            return;
        }

        // AJAX call to get leaderboard.
        $.ajax({
            url: "./getLeaderboard",
            type: "POST",
            data: {subjectUUID: subjectUUID},
            dataType: "json",
            success: function(res){
                if(res.status == "success"){
                    // Wrap in same panel + table styling as Top 10
                    let html = `<div class="tm-card tm-panel">
                                    <div class="tm-tableWrap">
                                        <table class="table table-striped align-middle mb-0">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>#</th>
                                                    <th>First Name</th>
                                                    <th>Last Name</th>
                                                    <th>Score</th>
                                                </tr>
                                            </thead>
                                            <tbody>`;

                    // Top 3 scores are colour coded.
                    res.data.forEach(function(student, index){
                        let medal = "";

                        // Top 3 replaced by colored trophies.
                        if(index === 0) {
                            medal = '<span class="material-icons trophy-gold">emoji_events</span>'; 
                        }
                        else if(index === 1) {
                            medal = '<span class="material-icons trophy-silver">emoji_events</span>'; 
                        }
                        else if(index === 2) {
                            medal = '<span class="material-icons trophy-bronze">emoji_events</span>'; 
                        }
                        else medal = index + 1; 

                        // All data escaped with htmlspecialchars in getLeaderboard.php.
                        html += `<tr>
                                    <td><strong>${medal}</strong></td>
                                    <td class="py-2">${student.firstName}</td>
                                    <td class="py-2">${student.lastName}</td>
                                    <td class="py-2">${student.totalScore}</td>
                                </tr>`;
                    });

                    html += `</tbody>
                            </table>
                        </div>
                    </div>`;

                    // inject table HTML but keep the wrapper.
                    $("#leaderboardResults .leaderboard-table").html(html).show();
                    $("#leaderboardResults .placeholder-text").hide();
                } else {
                    // Show error message in placeholder text.
                    $("#leaderboardResults .placeholder-text").show().text(res.message);
                    $("#leaderboardResults .leaderboard-table").hide();
                }
            },
            // If AJAX fails, display message.
            error:function(err){
                $("#leaderboardResults .placeholder-text").show().text("Failed to load leaderboard.");
                $("#leaderboardResults .leaderboard-table").hide();
            }
        });
    });
});
