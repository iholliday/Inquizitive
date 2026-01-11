const profileMenu = document.getElementById("profileMenu");
const profileTrigger = document.getElementById("profileTrigger");
const profileActions = document.getElementById("profileActions");

function openMenu() {
  profileMenu.classList.add("open");
  profileTrigger.setAttribute("aria-expanded", "true");
  profileActions.setAttribute("aria-hidden", "false");
}

function closeMenu() {
  profileMenu.classList.remove("open");
  profileTrigger.setAttribute("aria-expanded", "false");
  profileActions.setAttribute("aria-hidden", "true");
}

function toggleMenu() {
  const isOpen = profileMenu.classList.contains("open");
  if (isOpen) closeMenu();
  else openMenu();
}

profileTrigger.addEventListener("click", (e) => {
  e.stopPropagation();
  toggleMenu();
});

document.addEventListener("click", (e) => {
  if (!profileMenu.contains(e.target)) closeMenu();
});

document.addEventListener("keydown", (e) => {
  if (e.key === "Escape") closeMenu();
});
