// ==================== SIDEBAR PROFILE MENU ====================

// Get references to the profile menu elements
const profileMenu = document.getElementById("profileMenu");
const profileTrigger = document.getElementById("profileTrigger");
const profileActions = document.getElementById("profileActions");
  function setActiveNav(activeId) {
    $(".nav-item").removeClass("active");
    if (activeId) $("#" + activeId).addClass("active");
  }

  function loadIntoMain(url, activeId, { pushHistory = true } = {}) {
    $.ajax({
      url,
      type: "GET",
      success: function (data) {
        $("#content").html(data);

        // Give active class if page is selected
        setActiveNav(activeId);

        // Remember the last visited page (in case of refresh)
        if (activeId) sessionStorage.setItem("currentPage", activeId);

        // Allows for back and forward navigation
        if (pushHistory) {
          history.pushState({ url, activeId }, "", url);
        }
      },
      // Error message for if page is not found
      error: function (xhr, status, error) {
        console.error("Error loading page:", url, status, error);
        $("#content").html(`
          <div style="padding:16px;">
            <h2 style="margin:0 0 8px;">Couldn’t load that page</h2>
            <p style="margin:0;">Please try again.</p>
          </div>
        `);
      },
    });


    $.ajax({
      url:"./last-page",
      type: "POST",
      data:{lastPage:url},
      success: function (data) {
        console.log("Saved into session")
      },
      // Error message for if page is not found
      error: function (xhr, status, error) {
        console.error("Error loading page:", url, status, error);
      },
    });
  }

  // No .link
  $(function () {
    $(".link").on("click", function (e) {
      e.preventDefault();
      loadIntoMain($(this).attr("id"), $(this).attr("id"));
      // console.log removed
    });
  });


// ==================== PROFILE MENU ====================


function toggleMenu() {
  const isOpen = profileMenu.classList.contains("open");

  if (isOpen) {
    closeMenu();
    $("#h").addClass("hidden");     // hide after closing
  } else {
    openMenu();
    $("#h").removeClass("hidden")   // show before focusing
         .focus();
  }
}

// Opens the profile drop-up menu
function openMenu() {
  profileMenu.classList.add("open");
  profileTrigger.setAttribute("aria-expanded", "true");
  profileActions.setAttribute("aria-hidden", "false");
}

// Closes the profile drop-up menu
function closeMenu() {
  profileMenu.classList.remove("open");
  profileTrigger.setAttribute("aria-expanded", "false");
  profileActions.setAttribute("aria-hidden", "true");
}

$("#h").click(function(){toggleMenu();});


// Toggle the menu when the profile area is clicked
profileTrigger?.addEventListener("click", (e) => {
  e.stopPropagation();
  toggleMenu();
});

$("#h").on("keydown", function (e) {
  if (e.key === "Enter" || e.key === " ") {
    e.preventDefault();
    toggleMenu();
  }

  if (e.key === "Escape") {
    e.preventDefault();
    closeMenu();
    $("#h").addClass("hidden");
  }
});



// ==================== BURGER MENU ====================
const burger = document.getElementById("burgerToggle");
const sidebar = document.querySelector(".sidebar");
const overlay = document.getElementById("sidebarOverlay");

burger?.addEventListener("click", () => {
    sidebar.classList.toggle("open");
    overlay.classList.toggle("show");
});

overlay?.addEventListener("click", () => {
    sidebar.classList.remove("open");
    overlay.classList.remove("show");
});


$(sidebar).ready(function(){
  
});