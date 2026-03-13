<?php
require_once("./php/_connect.php");
$db = new inquizitiveDB();
$conn = $db->connect;

// TOMS CODE
// If user isn't logged in, redirect to landing page. Not AJAX as this check should happen befoe page loads.
  if (!isset($_SESSION['userUUID']) || ($_SESSION['accessLevel'] == "USER"))
  {
     header("Location:./");
     exit();
  }
// END OF TOMS CODE

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

$isEmbeddedInDashboard = defined('IN_DASHBOARD_SHELL');

$quizUUID = htmlspecialchars(mysqli_real_escape_string($db->connect,$_POST['quizGrab']));
if (!$isAjax && !$isEmbeddedInDashboard) {
  $DASH_INCLUDE = __FILE__;
  require __DIR__ . '/../dashboardNavigation.php';
  exit;
}

if (!isset($_POST['quizGrab'])) {
  http_response_code(400);
  echo "<div class='alert alert-danger'>Missing quizGrab</div>";
  exit;
}

$quizUUID = mysqli_real_escape_string($conn, $_POST['quizGrab']);

$quizMeta = null;
if ($res = $db->Query("CALL GetQuizMetaByUUID(?);", [$quizUUID])) {
  $quizMeta = mysqli_fetch_assoc($res);
  mysqli_free_result($res);
  while (mysqli_more_results($conn) && mysqli_next_result($conn)) {
    $junk = mysqli_store_result($conn);
    if ($junk) mysqli_free_result($junk);
  }
}
if (!$quizMeta) { echo "<div class='alert alert-warning'>Quiz not found.</div>"; exit; }

$questions = [];
if ($res = $db->Query("CALL GetQuizQuestionsByID(?);", [$quizUUID])) {
  while ($row = mysqli_fetch_assoc($res)) $questions[] = $row;
  mysqli_free_result($res);
  while (mysqli_more_results($conn) && mysqli_next_result($conn)) {
    $junk = mysqli_store_result($conn);
    if ($junk) mysqli_free_result($junk);
  }
}

$quizName   = htmlspecialchars($quizMeta['quizName'] ?? '', ENT_QUOTES, 'UTF-8');
$subject    = htmlspecialchars($quizMeta['subjectTitle'] ?? 'Unassigned', ENT_QUOTES, 'UTF-8');
$created    = htmlspecialchars($quizMeta['quizCreationDate'] ?? '', ENT_QUOTES, 'UTF-8');
$updated    = htmlspecialchars($quizMeta['lastUpdated'] ?? '', ENT_QUOTES, 'UTF-8');
?>

<div class="container-fluid py-3" id="lecturerDashboard">

    <button type="button" class="btn btn-questions" id="backBtn">
        <i class="bi bi-arrow-left-circle"></i> Back
    </button>

  <!-- Header -->
  <div class="card mb-3 shadow-sm">
    <div class="card-body d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
      <div>
        <h4 class="mb-1"><?php echo $quizName; ?></h4>
        <div class="text-muted">
          <span class="me-2"><strong>Subject:</strong> <?php echo $subject; ?></span>
          <span class="me-2"><strong>Created:</strong> <?php echo $created; ?></span>
          <span><strong>Last updated:</strong> <?php echo $updated; ?></span>
        </div>
      </div>

      <div class="d-flex gap-2">
        <button type="button" class="btn btn-questions" id="addQuestionBtn">
          <i class="bi bi-plus-lg"></i> Add question
        </button>
      </div>
    </div>
  </div>

  <?php if (count($questions) === 0): ?>
    <div class="alert alert-info">No questions found for this quiz.</div>
  <?php else: ?>

    <div class="card shadow-sm">
      <div class="card-body">

        <div class="qm-table-wrap table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th class="tblHeader">#</th>
                <th class="qm-question">Question</th>
                <th class="qm-option">A</th>
                <th class="qm-option">B</th>
                <th class="qm-option">C</th>
                <th class="qm-option">D</th>
                <th class="tblHeader;">Timer Duration</th>
                <th class="qm-actions text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
            <?php
              $i = 1;
              foreach ($questions as $q) {
                $questionUUID = htmlspecialchars($q['questionUUID'], ENT_QUOTES, 'UTF-8');
                $qText = htmlspecialchars($q['questionText'] ?? '', ENT_QUOTES, 'UTF-8');
                $correct = trim((string)($q['correctAnswer'] ?? ''));

                $aRaw = (string)($q['answerA'] ?? '');
                $bRaw = (string)($q['answerB'] ?? '');
                $cRaw = (string)($q['answerC'] ?? '');
                $dRaw = (string)($q['answerD'] ?? '');

                $a = htmlspecialchars($aRaw, ENT_QUOTES, 'UTF-8');
                $b = htmlspecialchars($bRaw, ENT_QUOTES, 'UTF-8');
                $c = htmlspecialchars($cRaw, ENT_QUOTES, 'UTF-8');
                $d = htmlspecialchars($dRaw, ENT_QUOTES, 'UTF-8');
                $aClass = (trim($aRaw) === trim($correct)) ? "qm-correct" : "";
                $bClass = (trim($bRaw) === trim($correct)) ? "qm-correct" : "";
                $cClass = (trim($cRaw) === trim($correct)) ? "qm-correct" : "";
                $dClass = (trim($dRaw) === trim($correct)) ? "qm-correct" : "";

                $points = htmlspecialchars((string)($q['difficultyPoints'] ?? ''), ENT_QUOTES, 'UTF-8');

                echo "
                  <tr>
                    <td>{$i}</td>
                    <td>{$qText}</td>
                    <td class='{$aClass}'>{$a}</td>
                    <td class='{$bClass}'>{$b}</td>
                    <td class='{$cClass}'>{$c}</td>
                    <td class='{$dClass}'>{$d}</td>
                    <td><span class='badge text-bg-secondary'>{$points}</span></td>
                    <td class='text-end qm-actions'>
                      <button
                        type='button'
                        class='btn btn-sm btn-outline-primary qm-edit'
                        data-question-uuid='{$questionUUID}'
                        data-quiz-uuid='" . htmlspecialchars($quizUUID, ENT_QUOTES, 'UTF-8') . "'
                      >
                        Edit
                      </button>

                      <button
                        type='button'
                        class='btn btn-sm btn-outline-danger qm-delete'
                        data-question-uuid='{$questionUUID}'
                        data-quiz-uuid='" . htmlspecialchars($quizUUID, ENT_QUOTES, 'UTF-8') . "'
                      >
                        Delete
                      </button>
                    </td>
                  </tr>
                ";
                $i++;
              }
            ?>
            </tbody>
          </table>
        </div>

      </div>
    </div>

  <?php endif; ?>
</div>


<script>
  function escapeHtml(s) {
    return String(s ?? "")
      .replaceAll("&", "&amp;")
      .replaceAll("<", "&lt;")
      .replaceAll(">", "&gt;")
      .replaceAll('"', "&quot;")
      .replaceAll("'", "&#039;");
  }

  function questionFormHtml(data = {}) {
    const qText = escapeHtml(data.questionText);
    const a = escapeHtml(data.answerA);
    const b = escapeHtml(data.answerB);
    const c = escapeHtml(data.answerC);
    const d = escapeHtml(data.answerD);
    const correct = escapeHtml(data.correctAnswer);
    const points = escapeHtml(data.difficultyPoints ?? 1);

    return `
      <div class="text-start">
        <div class="mb-2">
          <label class="form-label">Question</label>
          <textarea id="sw_qText" class="form-control" rows="3">${qText}</textarea>
        </div>

        <div class="row g-2">
          <div class="col-6">
            <label class="form-label">Answer A</label>
            <input id="sw_a" class="form-control" value="${a}">
          </div>
          <div class="col-6">
            <label class="form-label">Answer B</label>
            <input id="sw_b" class="form-control" value="${b}">
          </div>
          <div class="col-6">
            <label class="form-label">Answer C</label>
            <input id="sw_c" class="form-control" value="${c}">
          </div>
          <div class="col-6">
            <label class="form-label">Answer D</label>
            <input id="sw_d" class="form-control" value="${d}">
          </div>
        </div>

        <div class="row g-2 mt-1">
          <div class="col-8">
            <label class="form-label">Correct Answer (must match one of A–D)</label>
            <select id="sw_correctPick" class="form-select">
              <option value="">Pick correct option…</option>
              <option value="A">A</option>
              <option value="B">B</option>
              <option value="C">C</option>
              <option value="D">D</option>
            </select>
            <div class="form-text">We store the full text in <code>correctAnswer</code> (same as your DB).</div>
          </div>
          <div class="col-4">
            <label class="form-label">Timer Duration</label>
            <input id="sw_points" type="number" min="1" class="form-control" value="${points}">
          </div>
        </div>
      </div>
    `;
  }

  function getFormPayload(quizUUID, questionUUID = "") {
    const questionText = $("#sw_qText").val().trim();
    const answerA = $("#sw_a").val().trim();
    const answerB = $("#sw_b").val().trim();
    const answerC = $("#sw_c").val().trim();
    const answerD = $("#sw_d").val().trim();
    const pick = $("#sw_correctPick").val();
    const difficultyPoints = $("#sw_points").val().trim();

    const map = { A: answerA, B: answerB, C: answerC, D: answerD };
    const correctAnswer = map[pick] ?? "";

    return {
      mode: questionUUID ? "edit" : "add",
      quizUUID,
      questionUUID,
      questionText,
      answerA,
      answerB,
      answerC,
      answerD,
      correctAnswer,
      difficultyPoints
    };
  }

  function validatePayload(p) {
    if (!p.questionText) return "Question text is required.";
    if (!p.answerA || !p.answerB || !p.answerC || !p.answerD) return "All four answers (A–D) are required.";
    if (!p.correctAnswer) return "Pick which option (A–D) is correct.";
    if (!p.difficultyPoints || Number(p.difficultyPoints) < 1) return "Points must be 1 or higher.";
    const answers = [p.answerA, p.answerB, p.answerC, p.answerD].map(x => x.trim());
    if (!answers.includes(p.correctAnswer.trim())) return "Correct answer must match one of the options exactly.";
    return null;
  }

  async function openQuestionModal({ title, quizUUID, questionUUID = "", existing = null }) {
    let data = existing;
    if (questionUUID && !data) {
      const res = await $.ajax({
        url: "./php/questionGet.php",
        method: "POST",
        dataType: "json",
        data: { quizUUID, questionUUID }
      });
      data = res.data;
    }

    const html = questionFormHtml(data || {});
    const result = await Swal.fire({
      title,
      html,
      width: 900,
      showCancelButton: true,
      confirmButtonText: "Save",
      cancelButtonText: "Cancel",
      focusConfirm: false,
      didOpen: () => {
        const d = data || {};
        const correct = (d.correctAnswer ?? "").trim();
        const a = (d.answerA ?? "").trim();
        const b = (d.answerB ?? "").trim();
        const c = (d.answerC ?? "").trim();
        const dd = (d.answerD ?? "").trim();
        let pick = "";
        if (correct && correct === a) pick = "A";
        else if (correct && correct === b) pick = "B";
        else if (correct && correct === c) pick = "C";
        else if (correct && correct === dd) pick = "D";
        if (pick) $("#sw_correctPick").val(pick);
      },
      preConfirm: () => {
        const payload = getFormPayload(quizUUID, questionUUID);
        const err = validatePayload(payload);
        if (err) {
          Swal.showValidationMessage(err);
          return false;
        }
        return payload;
      }
    });

    if (!result.isConfirmed) return;
    await $.ajax({
      url: "./php/questionSave.php",
      method: "POST",
      dataType: "json",
      data: result.value
    });

    Swal.fire({ icon: "success", title: "Saved!", timer: 900, showConfirmButton: false });
    // IF NO PAGE RELOAD, WE RELOAD PAGE THROUGH AJAX!!
    location.reload();
  }

  // ADD
  $("#addQuestionBtn").on("click", function () {
    const quizUUID = "<?= htmlspecialchars($quizUUID, ENT_QUOTES, 'UTF-8') ?>";
    openQuestionModal({ title: "Add Question", quizUUID });
  });

  // EDIT
  $(document).on("click", ".qm-edit", function () {
    const quizUUID = $(this).data("quiz-uuid");
    const questionUUID = $(this).data("question-uuid");
    openQuestionModal({ title: "Edit Question", quizUUID, questionUUID });
  });

  // DELETE
  $(document).on("click", ".qm-delete", async function () {
    const quizUUID = $(this).data("quiz-uuid");
    const questionUUID = $(this).data("question-uuid");
    const row = $(this).closest("tr");

    const res = await Swal.fire({
      icon: "warning",
      title: "Delete this question?",
      text: "This cannot be undone.",
      showCancelButton: true,
      confirmButtonText: "Delete",
      cancelButtonText: "Cancel",
      confirmButtonColor: "#d33"
    });

    if (!res.isConfirmed) return;

    try {
      await $.ajax({
        url: "./php/questionDelete.php",
        method: "POST",
        dataType: "json",
        data: { quizUUID, questionUUID }
      });

      // Remove row instantly
      row.remove();

      Swal.fire({ icon: "success", title: "Deleted", timer: 900, showConfirmButton: false });

    } catch (e) {
      Swal.fire({ icon: "error", title: "Delete failed", text: (e.responseText || "Unknown error") });
    }
  });
</script>

<script>
$(document).on("click", "#backBtn", function (e) {
  e.preventDefault();

  $.ajax({
    url: "./test-management",
    type: "POST",
    headers: {
      "X-Requested-With": "XMLHttpRequest"
    },
    success: function (response) {
      $("#content").html(response);
    },
    error: function (xhr, status, error) {
      console.error("Failed to load quiz management page:", error);
      Swal.fire({
        icon: "error",
        title: "Could not go back",
        text: "Failed to load the quiz management page."
      });
    }
  });
});
</script>