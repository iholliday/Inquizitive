$(document).ready(function(){

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

        $.ajax({
            url: "./php/getLeaderboard",
            type: "POST",
            data: {subjectUUID: subjectUUID},
            dataType: "json",

            success: function(res){

                if(res.status === "success"){

                    let html = "";

                    res.data.forEach(function(student, index)
                    {
                        let medal = "";

                        if(index === 0) medal = '<span class="lb-gold">1</span>';
                        else if(index === 1) medal = '<span class="lb-silver">2</span>';
                        else if(index === 2) medal = '<span class="lb-bronze">3</span>';
                        else medal = index + 1;

                        html += `
                        <div class="mb-3">

                            <div class="d-flex justify-content-between">
                                <strong>${medal} ${student.firstName} ${student.lastName}</strong>
                                <span>${student.score}%</span>
                            </div>

                            <div class="progress">
                                <div class="progress-bar bg-success"
                                     style="width:${student.score}%">
                                </div>
                            </div>

                        </div>
                        `;
                    });

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

            error:function(){
                $("#leaderboardResults").html(`
                    <p class="text-danger text-center">
                        Failed to load leaderboard.
                    </p>
                `);
            }

        });

    });

});
