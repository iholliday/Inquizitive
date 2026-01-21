// ==================== SIDEBAR PROFILE MENU ====================
console.log("uwu");
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
          history.pushState({ url, activeId }, "", "#"+activeId);
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
  }

$(".link").ready(function(){
  $(".link").click(function(e)
  {
    e.preventDefault();
    loadIntoMain($(this).attr("id"), $(this).attr("id"));
    console.log($(this).attr("id"));
  });
});

// Opens the profile drop-up menu
function openMenu() {

  // Add class to display the menu
  profileMenu.classList.add("open");
  
  // Update accessibility attribute to indicate the menu is expanded
  profileTrigger.setAttribute("aria-expanded", "true");

  // Make the menu visible to screen readers
  profileActions.setAttribute("aria-hidden", "false");
}

// Closes the profile drop-up menu
function closeMenu() {

  // Remove class to hide the menu
  profileMenu.classList.remove("open");

  // Update accessibility attribute to indicate the menu is collapsed
  profileTrigger.setAttribute("aria-expanded", "false");

  // Hide the menu from screen readers
  profileActions.setAttribute("aria-hidden", "true");
}

// Toggles the profile menu open or closed
function toggleMenu() {

  // Check whether the menu is currently open
  const isOpen = profileMenu.classList.contains("open");

  // Open or close the menu based on its current state
  if (isOpen) closeMenu();
  else openMenu();
}

// Toggle the menu when the profile area is clicked
profileTrigger.addEventListener("click", (e) => {
  
  // Prevent the click from bubbling up to the document
  e.stopPropagation();

  // Open or closes the profile menu
  toggleMenu();
});

// Closes the profile menu when clicking anywhere outside of it
document.addEventListener("click", (e) => {

  // If the click target is not inside the profile menu, close it
  if (!profileMenu.contains(e.target)) closeMenu();
});

// Closes the profile menu when the Escape key is pressed
document.addEventListener("keydown", (e) => {
  
  // Check if the pressed key is Escape
  if (e.key === "Escape") closeMenu();
});

// ==================== AJAX PAGE LOADING ====================

document.addEventListener("DOMContentLoaded", () => {
  const contentSelector = "#content"; // <- This is selecting where the pages will load

  // Routes to map out where each page is, the route name is the same as the ID in the dashboardNavigation.php
  const routes = {
    // Main Dashboard
    dashboardPage: "./includes/inc-DashboardMain.php",
    quizPage: "./includes/inc-QuizzesDashboardPage.php",
    subjectsPage: "./includes/inc-SubjectsDashboardPage.php",
    resultsPage: "./includes/inc-ResultsDashboardPage.php",

    // Lecturer Dashboard
    studentManagement: "./includes/inc-LecStudentManagement.php",
    testManagement: "./includes/inc-LecTestManagement.php",

    // Admin Dashboard
    lecturerManagement: "./includes/inc-AdmlecturerManagement.php",

    // Drop Up Section
    profilePage: "./includes/inc-ProfileDashboardPage.php",
    settingsPage: "./includes/inc-SettingsPage.php",
  };

  // -------- Sidebar active state (only for nav items not drop up) --------


  // -------- Content loader --------


  // -------- One event listener for all route buttons --------
/*  document.addEventListener("click", (e) => {
    console.log("owo");
    // Find the closest clicked element that has an ID
    const el = e.target.closest("[id]");
    // Exit if no element with an ID is selected
    if (!el) return;

    // Get the ID of the clicked element
    const id = el.id;
    // Look up the corresponding route from the routes map
    const url = routes[id];
    // Exit if the ID does not map to a route
    if (!url) return; 

    e.preventDefault();

    // Load the requested page into the main content area with AJAX
    loadIntoMain(url, id);
  });*/

  // -------- Restore last visited page --------

  // Retrieving the last visited page ID from session storage
  const stored = sessionStorage.getItem("currentPage");

  // Define the default page to load if no previous page exists (dashboard page if user is logged in)
  const defaultId = "dashboardPage";

  // Decide which page to load on startup, use stored page if it exists, if not use the default set above
  const startId = stored && routes[stored] ? stored : defaultId;

  // Load the initual page into the main content area without updating browser history
  loadIntoMain(routes[startId], startId, { pushHistory: false });

  // -------- Back/forward support --------

  // Listen for back/forward navigation events
  window.addEventListener("popstate", (evt) => {

    // Check that a valid history state with a URL exists
    if (evt.state && evt.state.url) {

      // Load the page stored in the browser history without pushing a new history entry
      loadIntoMain(evt.state.url, evt.state.activeId, { pushHistory: false });
    }
  });
});


