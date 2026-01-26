/**
 * Modern Theme JavaScript
 * Handles sidebar toggle, dropdowns, and interactions
 */

(function() {
  'use strict';

  // Initialize when DOM is ready
  document.addEventListener('DOMContentLoaded', function() {
    initSidebar();
    initDropdowns();
    initMobileMenu();
    initSearch();
  });

  /**
   * Sidebar Toggle Functionality
   */
  function initSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const navbarToggle = document.querySelector('.navbar-toggle');
    const overlay = document.querySelector('.overlay');

    if (!sidebar) return;

    // Check localStorage for sidebar state
    const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
    if (isCollapsed) {
      sidebar.classList.add('collapsed');
    }

    // Sidebar toggle button (inside sidebar)
    if (sidebarToggle) {
      sidebarToggle.addEventListener('click', function(e) {
        e.preventDefault();
        toggleSidebar();
      });
    }

    // Navbar toggle button (top navbar)
    if (navbarToggle) {
      navbarToggle.addEventListener('click', function(e) {
        e.preventDefault();
        toggleSidebar();
      });
    }

    // Overlay click (mobile)
    if (overlay) {
      overlay.addEventListener('click', function() {
        if (window.innerWidth <= 768) {
          closeMobileSidebar();
        }
      });
    }

    // Handle window resize
    window.addEventListener('resize', function() {
      if (window.innerWidth > 768) {
        sidebar.classList.remove('show');
        if (overlay) overlay.classList.remove('show');
      }
    });

    function toggleSidebar() {
      sidebar.classList.toggle('collapsed');
      const isNowCollapsed = sidebar.classList.contains('collapsed');
      localStorage.setItem('sidebar-collapsed', isNowCollapsed);
    }
  }

  /**
   * Mobile Menu Toggle
   */
  function initMobileMenu() {
    const sidebar = document.querySelector('.sidebar');
    const navbarToggle = document.querySelector('.navbar-toggle');
    const overlay = document.querySelector('.overlay');

    if (!navbarToggle) return;

    navbarToggle.addEventListener('click', function(e) {
      e.preventDefault();
      
      if (window.innerWidth <= 768) {
        if (sidebar.classList.contains('show')) {
          closeMobileSidebar();
        } else {
          openMobileSidebar();
        }
      }
    });

    function openMobileSidebar() {
      sidebar.classList.add('show');
      if (overlay) overlay.classList.add('show');
      document.body.style.overflow = 'hidden';
    }

    function closeMobileSidebar() {
      sidebar.classList.remove('show');
      if (overlay) overlay.classList.remove('show');
      document.body.style.overflow = '';
    }
  }

  /**
   * Dropdown Menus
   */
  function initDropdowns() {
    const dropdowns = document.querySelectorAll('.user-menu');
    const userMenuToggle = document.getElementById('userMenuToggle');
    const userDropdown = document.getElementById('userDropdown');

    // Handle user menu toggle
    if (userMenuToggle && userDropdown) {
      userMenuToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        
        // Close other dropdowns
        dropdowns.forEach(function(otherDropdown) {
          const otherMenu = otherDropdown.querySelector('.dropdown-menu');
          if (otherMenu && otherMenu !== userDropdown) {
            otherMenu.classList.remove('show');
          }
        });

        // Toggle current dropdown
        userDropdown.classList.toggle('show');
      });
    }

    // Handle other dropdowns
    dropdowns.forEach(function(dropdown) {
      const toggle = dropdown.querySelector('.user-avatar, .dropdown-toggle');
      const menu = dropdown.querySelector('.dropdown-menu');

      if (!toggle || !menu || toggle === userMenuToggle) return;

      toggle.addEventListener('click', function(e) {
        e.stopPropagation();
        
        // Close other dropdowns
        dropdowns.forEach(function(otherDropdown) {
          if (otherDropdown !== dropdown) {
            const otherMenu = otherDropdown.querySelector('.dropdown-menu');
            if (otherMenu) otherMenu.classList.remove('show');
          }
        });

        // Toggle current dropdown
        menu.classList.toggle('show');
      });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
      if (!e.target.closest('.user-menu')) {
        dropdowns.forEach(function(dropdown) {
          const menu = dropdown.querySelector('.dropdown-menu');
          if (menu) menu.classList.remove('show');
        });
      }
    });
  }

  /**
   * Submenu Toggle
   */
  document.addEventListener('click', function(e) {
    const navItem = e.target.closest('.nav-item.has-submenu');
    if (!navItem) return;

    const navLink = navItem.querySelector('.nav-link');
    if (!navLink || !e.target.closest('.nav-link')) return;

    e.preventDefault();

    // Close other submenus
    document.querySelectorAll('.nav-item.has-submenu').forEach(function(item) {
      if (item !== navItem) {
        item.classList.remove('open');
      }
    });

    // Toggle current submenu
    navItem.classList.toggle('open');
  });

  /**
   * Search Functionality
   */
  function initSearch() {
    const searchInput = document.querySelector('.search-box input');
    if (!searchInput) return;

    // Add debounce for search
    let searchTimeout;
    searchInput.addEventListener('input', function(e) {
      clearTimeout(searchTimeout);
      const query = e.target.value.trim();

      searchTimeout = setTimeout(function() {
        if (query.length > 0) {
          performSearch(query);
        }
      }, 300);
    });
  }

  /**
   * Perform search (can be customized)
   */
  function performSearch(query) {
    console.log('Searching for:', query);
    // Add your search logic here
    // This could trigger DataTable search, filter components, etc.
    
    // Example: Trigger DataTable search if available
    if (typeof $.fn.DataTable !== 'undefined') {
      $('.data-table').each(function() {
        const table = $(this).DataTable();
        if (table) {
          table.search(query).draw();
        }
      });
    }
  }

  /**
   * Active Menu Item Highlighting
   */
  function highlightActiveMenu() {
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.sidebar-nav .nav-link');

    navLinks.forEach(function(link) {
      const href = link.getAttribute('href');
      if (href && currentPath.includes(href) && href !== '/') {
        link.classList.add('active');
        
        // Open parent submenu if exists
        const navItem = link.closest('.nav-item');
        if (navItem && navItem.classList.contains('has-submenu')) {
          navItem.classList.add('open');
        }
      }
    });
  }

  // Call on page load
  highlightActiveMenu();

  /**
   * Smooth Scroll
   */
  document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
    anchor.addEventListener('click', function(e) {
      const href = this.getAttribute('href');
      if (href === '#' || href === '') return;

      const target = document.querySelector(href);
      if (target) {
        e.preventDefault();
        target.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
      }
    });
  });

  /**
   * Form Validation Enhancement
   */
  const forms = document.querySelectorAll('form[data-validate]');
  forms.forEach(function(form) {
    form.addEventListener('submit', function(e) {
      if (!form.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
      }
      form.classList.add('was-validated');
    });
  });

})();

