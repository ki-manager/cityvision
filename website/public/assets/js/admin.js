/**
 * Admin-JavaScript: Löschen-Bestätigungen.
 */
(function () {
  'use strict';

  document.addEventListener('submit', function (event) {
    var form = event.target;
    if (!(form instanceof HTMLFormElement)) {
      return;
    }
    var confirmMessage = form.getAttribute('data-confirm');
    if (confirmMessage && !window.confirm(confirmMessage)) {
      event.preventDefault();
    }
  });
})();
