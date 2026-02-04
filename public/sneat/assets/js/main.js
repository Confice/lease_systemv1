/**
 * Main
 */

'use strict';

let menu, animate;

(function () {
  // Initialize menu
  //-----------------

  let layoutMenuEl = document.querySelectorAll('#layout-menu');
  layoutMenuEl.forEach(function (element) {
    menu = new Menu(element, {
      orientation: 'vertical',
      closeChildren: false
    });
    // Change parameter to true if you want scroll animation
    window.Helpers.scrollToActive((animate = false));
    window.Helpers.mainMenu = menu;
  });

  // Initialize sidebar toggle button - Works on ALL screen sizes
  function initSidebarToggle() {
    const STORAGE_KEY = 'sidebar-collapsed-state';
    const layoutWrapper = document.querySelector('.layout-wrapper');
    
    // Restore sidebar state from localStorage on page load (desktop only; mobile always starts collapsed)
    function restoreSidebarState() {
      if (!layoutWrapper) return;
      
      if (window.innerWidth < 1200) {
        // Mobile/tablet: always start with sidebar hidden and no overlay
        layoutWrapper.classList.add('layout-menu-collapsed');
        var overlay = document.querySelector('.layout-overlay');
        if (overlay) overlay.classList.remove('show');
      } else {
        const savedState = localStorage.getItem(STORAGE_KEY);
        if (savedState === 'true') {
          layoutWrapper.classList.add('layout-menu-collapsed');
        } else {
          layoutWrapper.classList.remove('layout-menu-collapsed');
        }
      }
    }

    // Restore state immediately
    restoreSidebarState();

    function toggleSidebarAndOverlay() {
      if (!layoutWrapper) return;
      layoutWrapper.classList.toggle('layout-menu-collapsed');
      var isCollapsed = layoutWrapper.classList.contains('layout-menu-collapsed');
      localStorage.setItem(STORAGE_KEY, isCollapsed ? 'true' : 'false');
      if (window.innerWidth < 1200) {
        var overlay = document.querySelector('.layout-overlay');
        if (overlay) {
          if (isCollapsed) {
            overlay.classList.remove('show');
          } else {
            overlay.classList.add('show');
          }
        }
      }
    }

    // Handle the sidebar toggle button (circular button on sidebar)
    var sidebarToggleBtn = document.getElementById('sidebar-toggle-btn');
    if (sidebarToggleBtn) {
      var newBtn = sidebarToggleBtn.cloneNode(true);
      sidebarToggleBtn.parentNode.replaceChild(newBtn, sidebarToggleBtn);
      newBtn.addEventListener('click', function(event) {
        event.preventDefault();
        event.stopPropagation();
        toggleSidebarAndOverlay();
      });
    }

    // Hamburger menu (navbar): use delegation so it works on mobile for the whole system
    document.addEventListener('click', function(event) {
      if (event.target.closest('.layout-navbar .layout-menu-toggle') && !event.target.closest('.layout-overlay')) {
        event.preventDefault();
        event.stopPropagation();
        if (window.innerWidth < 1200) {
          toggleSidebarAndOverlay();
        }
      }
    });

    // Close sidebar when clicking overlay on mobile
    document.addEventListener('click', function(e) {
      if (window.innerWidth >= 1200) return;
      if (e.target.classList.contains('layout-overlay') && e.target.classList.contains('show')) {
        layoutWrapper.classList.add('layout-menu-collapsed');
        localStorage.setItem(STORAGE_KEY, 'true');
        e.target.classList.remove('show');
      }
    });
  }

  // Initialize sidebar toggle immediately and also on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSidebarToggle);
  } else {
    initSidebarToggle();
  }

  // Handle collapsed sidebar dropdown menu clicks
  function initCollapsedDropdowns() {
    const layoutWrapper = document.querySelector('.layout-wrapper');
    let collapseTimeout = null;
    
    // Delegate click events to handle dynamically added menus
    document.addEventListener('click', function(event) {
      const toggleLink = event.target.closest('.menu-toggle');
      const submenuLink = event.target.closest('.menu-sub .menu-link');
      
      // Check if sidebar is collapsed
      if (!layoutWrapper || !layoutWrapper.classList.contains('layout-menu-collapsed')) {
        return;
      }
      
      // Handle clicking a menu toggle in collapsed state
      if (toggleLink) {
        const menuItem = toggleLink.closest('.menu-item');
        if (!menuItem) return;
        
        event.preventDefault();
        event.stopPropagation();
        
        // Temporarily expand sidebar to show submenu
        layoutWrapper.classList.remove('layout-menu-collapsed');
        
        // If submenu is not open, open it
        if (!menuItem.classList.contains('open')) {
          menuItem.classList.add('open');
        }
        
        // Collapse sidebar back after a short delay when clicking outside
        if (collapseTimeout) {
          clearTimeout(collapseTimeout);
        }
        
        collapseTimeout = setTimeout(function() {
          document.addEventListener('click', function collapseHandler(e) {
            const isMenuClick = e.target.closest('.menu-item') !== null || e.target.closest('.menu-sub') !== null;
            const isSidebarClick = e.target.closest('.layout-menu') !== null;
            
            if (!isSidebarClick && !isMenuClick) {
              layoutWrapper.classList.add('layout-menu-collapsed');
              document.removeEventListener('click', collapseHandler);
              collapseTimeout = null;
            }
          });
        }, 100);
      }
      
      // Handle clicking a submenu item - collapse sidebar after navigation
      if (submenuLink) {
        if (collapseTimeout) {
          clearTimeout(collapseTimeout);
        }
        // Collapse immediately when a submenu link is clicked
        setTimeout(function() {
          layoutWrapper.classList.add('layout-menu-collapsed');
        }, 100);
      }
    });
  }

  // Initialize collapsed dropdowns
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCollapsedDropdowns);
  } else {
    initCollapsedDropdowns();
  }

  // Display menu toggle (layout-menu-toggle) on hover with delay - only target SIDEBAR toggle, not navbar hamburger
  var sidebarMenuToggleEl = document.querySelector('#layout-menu .layout-menu-toggle') || document.querySelector('.app-brand .layout-menu-toggle');
  let delay = function (elem, callback) {
    let timeout = null;
    elem.onmouseenter = function () {
      // Set timeout to be a timer which will invoke callback after 300ms (not for small screen)
      if (!Helpers.isSmallScreen()) {
        timeout = setTimeout(callback, 300);
      } else {
        timeout = setTimeout(callback, 0);
      }
    };

    elem.onmouseleave = function () {
      // Clear any timers set to timeout - only affect sidebar toggle, never navbar hamburger
      if (sidebarMenuToggleEl) {
        sidebarMenuToggleEl.classList.remove('d-block');
      }
      clearTimeout(timeout);
    };
  };
  if (document.getElementById('layout-menu')) {
    delay(document.getElementById('layout-menu'), function () {
      // not for small screen
      if (!Helpers.isSmallScreen() && sidebarMenuToggleEl) {
        sidebarMenuToggleEl.classList.add('d-block');
      }
    });
  }

  // Display in main menu when menu scrolls
  let menuInnerContainer = document.getElementsByClassName('menu-inner'),
    menuInnerShadow = document.getElementsByClassName('menu-inner-shadow')[0];
  if (menuInnerContainer.length > 0 && menuInnerShadow) {
    menuInnerContainer[0].addEventListener('ps-scroll-y', function () {
      if (this.querySelector('.ps__thumb-y').offsetTop) {
        menuInnerShadow.style.display = 'block';
      } else {
        menuInnerShadow.style.display = 'none';
      }
    });
  }

  // Init helpers & misc
  // --------------------

  // Init BS Tooltip
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });

  // Accordion active class
  const accordionActiveFunction = function (e) {
    if (e.type == 'show.bs.collapse' || e.type == 'show.bs.collapse') {
      e.target.closest('.accordion-item').classList.add('active');
    } else {
      e.target.closest('.accordion-item').classList.remove('active');
    }
  };

  const accordionTriggerList = [].slice.call(document.querySelectorAll('.accordion'));
  const accordionList = accordionTriggerList.map(function (accordionTriggerEl) {
    accordionTriggerEl.addEventListener('show.bs.collapse', accordionActiveFunction);
    accordionTriggerEl.addEventListener('hide.bs.collapse', accordionActiveFunction);
  });

  // Auto update layout based on screen size
  window.Helpers.setAutoUpdate(true);

  // Toggle Password Visibility
  window.Helpers.initPasswordToggle();

  // Speech To Text
  window.Helpers.initSpeechToText();

  // Manage menu expanded/collapsed with templateCustomizer & local storage
  //------------------------------------------------------------------

  // If current layout is horizontal OR current window screen is small (overlay menu) than return from here
  if (window.Helpers.isSmallScreen()) {
    return;
  }

  // If current layout is vertical and current window screen is > small

  // Auto update menu collapsed/expanded based on the themeConfig
  // Start with expanded menu (false = not collapsed)
  window.Helpers.setCollapsed(false, false);
})();

$(function(){
    // Universal search function
    function globalSearch(query){
        // Users table
        if($('#usersTable').length){
            $('#usersTable').DataTable().search(query).draw();
        }

        // Dashboard table (example)
        if($('#dashboardTable').length){
            $('#dashboardTable').DataTable().search(query).draw();
        }

        // Stalls table
        if($('#stallsTable').length){
            $('#stallsTable').DataTable().search(query).draw();
        }

        // Add more tables here as needed
    }

    // Bind topbar search input
    $('#globalSearch').on('keyup', function(){
        globalSearch($(this).val());
    });

    // Clear search input when switching menu links
    $(document).on('click', '.menu-link', function(){
        $('#globalSearch').val('');
    });
});
