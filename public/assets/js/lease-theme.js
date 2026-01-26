/**
 * Lease Management System - Custom JavaScript
 * Handles sidebar toggle, dropdowns, and all interactions
 */

(function() {
  'use strict';

  // Initialize when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  function init() {
    initSidebar();
    initDropdowns();
    initMobileMenu();
    initSearch();
    highlightActiveMenu();
  }

  /**
   * Sidebar Toggle Functionality
   */
  function initSidebar() {
    const sidebar = document.querySelector('.lease-sidebar');
    const toggleBtn = document.querySelector('.lease-sidebar-toggle-btn');
    const STORAGE_KEY = 'lease-sidebar-collapsed';

    if (!sidebar) return;

    // Restore sidebar state from localStorage
    const isCollapsed = localStorage.getItem(STORAGE_KEY) === 'true';
    if (isCollapsed) {
      sidebar.classList.add('collapsed');
    }

    // Handle toggle button click
    if (toggleBtn) {
      toggleBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        toggleSidebar();
      });
    }

    function toggleSidebar() {
      sidebar.classList.toggle('collapsed');
      const isNowCollapsed = sidebar.classList.contains('collapsed');
      localStorage.setItem(STORAGE_KEY, isNowCollapsed);
    }
  }

  /**
   * Mobile Menu Toggle
   */
  function initMobileMenu() {
    const sidebar = document.querySelector('.lease-sidebar');
    const overlay = document.querySelector('.lease-overlay');

    if (!sidebar || window.innerWidth > 768) return;

    // Handle overlay click
    if (overlay) {
      overlay.addEventListener('click', function() {
        closeMobileSidebar();
      });
    }

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

    // Handle window resize
    window.addEventListener('resize', function() {
      if (window.innerWidth > 768) {
        sidebar.classList.remove('show');
        if (overlay) overlay.classList.remove('show');
        document.body.style.overflow = '';
      }
    });
  }

  /**
   * Dropdown Menus
   */
  function initDropdowns() {
    const userMenu = document.querySelector('.lease-user-menu');
    const userAvatar = document.querySelector('.lease-user-avatar');
    const userDropdown = document.querySelector('.lease-dropdown-menu');

    if (userAvatar && userDropdown) {
      userAvatar.addEventListener('click', function(e) {
        e.stopPropagation();
        
        // Close other dropdowns
        document.querySelectorAll('.lease-dropdown-menu').forEach(function(menu) {
          if (menu !== userDropdown) {
            menu.classList.remove('show');
          }
        });

        // Toggle current dropdown
        userDropdown.classList.toggle('show');
      });
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
      if (!e.target.closest('.lease-user-menu')) {
        document.querySelectorAll('.lease-dropdown-menu').forEach(function(menu) {
          menu.classList.remove('show');
        });
      }
    });
  }

  /**
   * Submenu Toggle
   */
  document.addEventListener('click', function(e) {
    const navItem = e.target.closest('.lease-nav-item.has-submenu');
    if (!navItem) return;

    const navLink = navItem.querySelector('.lease-nav-link');
    if (!navLink || !e.target.closest('.lease-nav-link')) return;

    e.preventDefault();

    // Close other submenus
    document.querySelectorAll('.lease-nav-item.has-submenu').forEach(function(item) {
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
    const searchInput = document.querySelector('.lease-topbar-search input, #globalSearch');
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
    
    // Trigger DataTable search if available
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
      $('.data-table, #usersTable, #dashboardTable, #stallsTable').each(function() {
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
    const navLinks = document.querySelectorAll('.lease-nav-link');

    navLinks.forEach(function(link) {
      const href = link.getAttribute('href');
      if (href && currentPath.includes(href) && href !== '/') {
        link.classList.add('active');
        
        // Open parent submenu if exists
        const navItem = link.closest('.lease-nav-item');
        if (navItem && navItem.classList.contains('has-submenu')) {
          navItem.classList.add('open');
        }

        // Also mark parent nav item as active
        const parentItem = link.closest('.lease-nav-item');
        if (parentItem) {
          parentItem.classList.add('active');
        }
      }
    });
  }

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

  // Make functions globally available if needed
  window.LeaseTheme = {
    toggleSidebar: function() {
      const sidebar = document.querySelector('.lease-sidebar');
      if (sidebar) {
        sidebar.classList.toggle('collapsed');
        localStorage.setItem('lease-sidebar-collapsed', sidebar.classList.contains('collapsed'));
      }
    }
  };
})();

