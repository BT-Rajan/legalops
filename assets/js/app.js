(function () {
  'use strict';

  // --- Mobile nav rail toggle -------------------------------------------
  var menuToggle = document.querySelector('[data-menu-toggle]');
  var rail = document.querySelector('.rail');
  var scrim = document.querySelector('.scrim');

  function closeRail() {
    if (rail) rail.classList.remove('open');
    if (scrim) scrim.classList.remove('open');
  }

  if (menuToggle && rail) {
    menuToggle.addEventListener('click', function () {
      rail.classList.toggle('open');
      if (scrim) scrim.classList.toggle('open');
    });
  }
  if (scrim) scrim.addEventListener('click', closeRail);

  // --- Theme toggle (light/dark), remembered via cookie ------------------
  var themeToggle = document.querySelector('[data-theme-toggle]');
  function setTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    document.cookie = 'legalops_theme=' + theme + ';path=' + (window.APP_BASE_PATH || '/') + ';max-age=31536000';
  }
  if (themeToggle) {
    themeToggle.addEventListener('click', function () {
      var current = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
      setTheme(current);
    });
  }

  // --- Generic modal open/close ------------------------------------------
  document.querySelectorAll('[data-open-modal]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var el = document.getElementById(btn.getAttribute('data-open-modal'));
      if (el) el.classList.add('open');
    });
  });
  document.querySelectorAll('[data-close-modal]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var overlay = btn.closest('.modal-overlay');
      if (overlay) overlay.classList.remove('open');
    });
  });
  document.querySelectorAll('.modal-overlay').forEach(function (overlay) {
    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) overlay.classList.remove('open');
    });
  });

  // --- Command-bar search: '/' focuses it, Esc blurs ----------------------
  var search = document.querySelector('[data-global-search]');
  document.addEventListener('keydown', function (e) {
    if (e.key === '/' && document.activeElement.tagName !== 'INPUT') {
      e.preventDefault();
      if (search) search.focus();
    }
    if (e.key === 'Escape' && search) {
      search.blur();
    }
  });

  // --- Auto-dismiss flash alerts ------------------------------------------
  document.querySelectorAll('[data-flash]').forEach(function (el) {
    setTimeout(function () {
      el.style.transition = 'opacity .4s ease';
      el.style.opacity = '0';
      setTimeout(function () { el.remove(); }, 400);
    }, 4500);
  });
})();
