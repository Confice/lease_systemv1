/**
 * Global SweetAlert Configuration
 * Removes animations from all toast notifications system-wide
 * This automatically applies to all Swal.fire() calls with toast: true
 */
(function() {
  'use strict';

  function applyNoAnimationToSwal() {
    if (typeof Swal === 'undefined') {
      return false;
    }

    // Store original fire function if not already wrapped
    if (!Swal._originalFire) {
      Swal._originalFire = Swal.fire;
    } else {
      return true; // Already wrapped
    }

    // Override Swal.fire to automatically disable animations for toasts
    Swal.fire = function(options) {
      // If options is an object and it's a toast notification
      if (options && typeof options === 'object' && options.toast === true) {
        // Automatically disable animations
        options.animation = false;
        options.showClass = options.showClass || {};
        options.hideClass = options.hideClass || {};
        
        // Remove any animation classes
        if (!options.showClass.popup) {
          options.showClass.popup = '';
        }
        if (!options.hideClass.popup) {
          options.hideClass.popup = '';
        }
      }
      
      // Call original fire function
      return Swal._originalFire.call(this, options);
    };

    return true;
  }

  // Try to apply immediately if Swal is already loaded
  if (!applyNoAnimationToSwal()) {
    // Wait for DOMContentLoaded
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', function() {
        applyNoAnimationToSwal();
      });
    }

    // Also check periodically in case Swal loads asynchronously
    let attempts = 0;
    const maxAttempts = 50; // Check for 5 seconds (50 * 100ms)
    const checkInterval = setInterval(function() {
      attempts++;
      if (applyNoAnimationToSwal() || attempts >= maxAttempts) {
        clearInterval(checkInterval);
      }
    }, 100);
  }
})();

