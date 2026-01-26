@push('styles')
<style>
  .page-header-center {
    text-align: center;
    margin-bottom: 0;
    margin-top: 0;
  }
  
  .page-header-center h2 {
    font-weight: bold;
    margin: 0;
  }
  
  .marketplace-levels-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    width: 100%;
    max-width: 1600px;
    margin: 0 auto;
  }
  
  .marketplace-levels-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    width: 100%;
    max-width: 1600px;
    margin: 1.5rem auto 0 auto;
    align-items: stretch; /* Ensure both level containers have same height */
  }
  
  .level-container {
    display: flex;
    flex-direction: column;
  }
  
  .level-header {
    text-align: center;
    font-weight: bold;
    margin-bottom: 0;
    margin-top: 1.5rem;
    font-size: 1.3rem;
    color: #000000;
  }
  
  .level-header:first-of-type {
    margin-top: 1.5rem;
  }
  
  .floorplan-wrapper {
    background: #EFEFEA;
    padding: 30px;
    border-radius: 8px;
    border: 2px solid #7F9267;
    position: relative;
    overflow: visible; /* Allow hover card to be positioned, but constrained by JS */
    flex: 1; /* Allow wrapper to fill container and match heights */
    display: flex;
    flex-direction: column;
  }
  
  .floorplan-grid {
    display: grid;
    gap: 20px;
    width: 100%;
    max-width: 1400px;
    margin: 0 auto;
    flex: 1; /* Allow grid to fill wrapper */
  }
  
  .floorplan-grid.hub-layout.level1 {
    grid-template-columns: repeat(8, 1fr);
    grid-template-rows: 120px 120px 120px 30px minmax(120px, auto) minmax(120px, auto);
    gap: 8px;
    grid-auto-flow: row;
    min-height: calc(3 * 120px + 30px + 2 * 120px + 5 * 8px); /* Ensure consistent height: 3 rows + spacing row + 2 rows + gaps */
  }
  
  .floorplan-grid.hub-layout.level2 {
    grid-template-columns: repeat(8, 1fr);
    grid-template-rows: 120px 120px 120px 30px minmax(120px, auto) minmax(120px, auto);
    gap: 8px;
    grid-auto-flow: row;
    min-height: calc(3 * 120px + 30px + 2 * 120px + 5 * 8px); /* Ensure same height as Level 1: 3 rows + spacing row + 2 rows + gaps */
  }
  
  .stall-room {
    border: 3px solid #000000;
    border-radius: 4px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 15px 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    background: #FEFEFE;
    min-height: 140px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }
  
  .stall-room::before {
    content: '';
    position: absolute;
    top: -3px;
    left: 50%;
    transform: translateX(-50%);
    width: 30px;
    height: 3px;
    background: #000000;
    border-radius: 2px 2px 0 0;
  }
  
  .stall-room.occupied {
    background-color: #E4E6DD !important;
    border-color: rgba(127, 146, 103, 0.2) !important;
    color: #788A61;
  }
  
  .stall-room.occupied::before {
    background: rgba(127, 146, 103, 0.2);
  }
  
  .stall-room.vacant {
    background-color: #ffe0db !important;
    border-color: #ffb2a5 !important;
    color: #ff3e1d;
  }
  
  .stall-room.vacant::before {
    background: #ffb2a5;
  }
  
  .stall-room:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    z-index: 10;
  }
  
  .stall-room-name {
    font-weight: bold;
    font-size: 16px;
    margin-bottom: 8px;
    text-align: center;
  }
  
  .stall-room.occupied .stall-room-name {
    color: #788A61;
  }
  
  .stall-room.vacant .stall-room-name {
    color: #ff3e1d;
  }
  
  .stall-room-size {
    font-size: 12px;
    text-align: center;
    font-weight: 500;
  }
  
  .stall-room.occupied .stall-room-size {
    color: #788A61;
  }
  
  .stall-room.vacant .stall-room-size {
    color: #ff3e1d;
  }
  
  .stall-info-card {
    position: absolute;
    background: #FEFEFE;
    border: 2px solid #7F9267;
    border-radius: 12px;
    padding: 0;
    min-width: 280px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
    z-index: 1000;
    display: none;
    overflow: hidden;
  }
  
  .stall-info-card.show {
    display: block;
  }
  
  .stall-info-card-header {
    background: linear-gradient(135deg, #7F9267 0%, #6B7A56 100%);
    padding: 15px 20px;
    color: #FFFFFF;
    font-weight: bold;
    text-align: center;
    border-bottom: 2px solid #6B7A56;
    border-radius: 10px 10px 0 0;
  }
  
  .stall-info-card-header .badge.occupied-badge {
    background-color: #E4E6DD !important;
    border: 2px solid #E4E6DD !important;
    color: #788A61 !important;
  }
  
  .stall-info-card-body {
    padding: 20px;
    background: #FEFEFE;
  }
  
  .stall-info-item {
    margin-bottom: 12px;
    padding-bottom: 12px;
    border-bottom: 1px solid #EFEFEA;
  }
  
  .stall-info-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
  }
  
  .stall-info-label {
    font-size: 11px;
    color: #6c757d;
    text-transform: uppercase;
    font-weight: 600;
    margin-bottom: 4px;
    letter-spacing: 0.5px;
  }
  
  .stall-info-value {
    font-size: 14px;
    color: #000000;
    font-weight: 500;
  }
  
  .stall-info-card-footer {
    padding: 15px 20px;
    background: #EFEFEA;
    border-top: 1px solid #7F9267;
  }
  
  .legend {
    display: flex;
    gap: 30px;
    margin-bottom: 0;
    margin-top: 1.5rem;
    padding: 20px;
    background: #FEFEFE;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    justify-content: center;
    border: 1px solid #7F9267;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
  }
  
  .legend-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 500;
    color: #000000;
  }
  
  .legend-color {
    width: 35px;
    height: 35px;
    border-radius: 4px;
    border: 2px solid;
  }
  
  .legend-color.occupied {
    background-color: #E4E6DD;
    border-color: rgba(127, 146, 103, 0.2);
  }
  
  .legend-color.vacant {
    background-color: #ffe0db;
    border-color: #ffb2a5;
  }
  
  .stall-room[data-stall-number="1"] {
    grid-column: 1;
    grid-row: 1;
  }
  
  .stall-room[data-stall-number="2"] {
    grid-column: 1;
    grid-row: 2;
  }
  
  .stall-room[data-stall-number="3"] {
    grid-column: 6;
    grid-row: 1;
  }
  
  .stall-room[data-stall-number="4"] {
    grid-column: 3;
    grid-row: 2;
  }
  
  .stall-room[data-stall-number="5"] {
    grid-column: 4;
    grid-row: 2;
  }
</style>
@endpush

<div class="page-header-center">
  <h2>Commercial Spaces at The Hub</h2>
</div>

<!-- Legend -->
<div class="legend">
  <div class="legend-item">
    <div class="legend-color occupied"></div>
    <span>Occupied</span>
  </div>
  <div class="legend-item">
    <div class="legend-color vacant"></div>
    <span>Vacant</span>
  </div>
</div>

<!-- Two Column Layout: Level 1 (Left) and Level 2 (Right) -->
<div class="marketplace-levels-container">
  <!-- Level 1 (Left Side) -->
  <div class="level-container">
    <div class="level-header">Level 1</div>
    <div class="floorplan-wrapper">
      <div class="floorplan-grid hub-layout" id="floorplanGridHub1">
        <!-- Stalls will be dynamically inserted here -->
      </div>
      <div class="stall-info-card" id="stallInfoCardHub1">
        <div class="stall-info-card-header" id="cardHeaderHub1"></div>
        <div class="stall-info-card-body" id="cardBodyHub1"></div>
        <div class="stall-info-card-footer" id="cardFooterHub1"></div>
      </div>
    </div>
  </div>

  <!-- Level 2 (Right Side) -->
  <div class="level-container">
    <div class="level-header">Level 2</div>
    <div class="floorplan-wrapper">
      <div class="floorplan-grid hub-layout" id="floorplanGridHub2">
        <!-- Stalls will be dynamically inserted here -->
      </div>
      <div class="stall-info-card" id="stallInfoCardHub2">
        <div class="stall-info-card-header" id="cardHeaderHub2"></div>
        <div class="stall-info-card-body" id="cardBodyHub2"></div>
        <div class="stall-info-card-footer" id="cardFooterHub2"></div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
$(function(){
  const marketplaceName = 'The Hub';
  let stallsData = [];
  let currentHoveredStall = {1: null, 2: null};
  
  // Map stall names to levels and positions for The Hub
  // Level 1: L1-1, L1-2, L1-3, L1-4, L1-5
  // All three corner stalls (L1-1, L1-2, L1-3) have the same width, based on L1-1
  // Grid positions for Level 1 stalls:
    // Rows 1-3: Central Block - L1-4 and L1-5 (same width and height, spans 3 rows, 3 cols each, side by side)
    // Row 4: Empty (padding/spacing below central block)
    // Row 5: L1-1 (just above L1-2) - basis width for all three corner stalls
    // Row 6: L1-2 (bottom-left), L1-3 (bottom-right corner) - both same width as L1-1
    const hubLevel1Positions = {
      'L1-1': { gridRow: '5', gridColumn: '1 / 3', level: 1 },  // Just above L1-2, basis width (spans 2 cols)
      'L1-2': { gridRow: '6', gridColumn: '1 / 3', level: 1 },  // Bottom-left corner, same width as L1-1 (spans 2 cols)
      'L1-3': { gridRow: '6', gridColumn: '7 / 9', level: 1 },  // Bottom-right corner, same width as L1-1 (spans 2 cols, at far right)
      'L1-4': { gridRow: '1 / 4', gridColumn: '2 / 5', level: 1 },  // Top-center, same size as L1-5 (spans 3 rows, 3 cols, centered with equal gaps)
      'L1-5': { gridRow: '1 / 4', gridColumn: '5 / 8', level: 1 }   // Top-center, same size as L1-4, beside L1-4 (spans 3 rows, 3 cols, centered with equal gaps)
    };
  
  // Grid positions for Level 2 stalls:
  // Rows 1-3: Central Block - L2-6 and L2-7 (same width and height as L1-4 and L1-5, spans 3 rows, 3 cols each, side by side)
  // Row 4: Empty (padding/spacing below central block)
  // Rows 5-6: Empty (to match Level 1 height)
  const hubLevel2Positions = {
    'L2-6': { gridRow: '1 / 4', gridColumn: '2 / 5', level: 2 },  // Top-center, same size as L2-7 (spans 3 rows, 3 cols, centered with equal gaps)
    'L2-7': { gridRow: '1 / 4', gridColumn: '5 / 8', level: 2 }   // Top-center, same size as L2-6, beside L2-6 (spans 3 rows, 3 cols, centered with equal gaps)
  };
  
  // Combined positions map
  const allHubPositions = { ...hubLevel1Positions, ...hubLevel2Positions };
  
  $.get('/marketplace/stalls', { marketplace: marketplaceName }, function(response) {
    stallsData = response.data || [];
    console.log('Hub stalls data:', stallsData);
    console.log('Hub stall names:', stallsData.map(s => s.stallNo));
    renderStalls();
  });
  
  function renderStalls() {
    renderLevel(1);
    renderLevel(2);
  }
  
  function renderLevel(level) {
    const grid = $(`#floorplanGridHub${level}`);
    grid.empty();
    if (level === 1) {
      grid.addClass('level1');
    } else {
      grid.addClass('level2');
    }
    
    // Filter stalls by level based on stall name (L1-* = level 1, L2-* = level 2)
    // If no position mapping, fall back to index-based splitting
    const levelStalls = stallsData.filter(function(stall) {
      const stallName = (stall.stallNo || '').toUpperCase().trim();
      const position = allHubPositions[stallName];
      if (position) {
        return position.level === level;
      }
      // Fallback: if no position mapping, use index-based splitting
      if (level === 1) {
        const index = stallsData.indexOf(stall);
        return index < Math.ceil(stallsData.length / 2);
      } else {
        const index = stallsData.indexOf(stall);
        return index >= Math.ceil(stallsData.length / 2);
      }
    });
    
    levelStalls.forEach(function(stall) {
      const stallName = (stall.stallNo || '').toUpperCase().trim();
      const isOccupied = stall.isOccupied;
      
      // Get position mapping by stall name
      const position = allHubPositions[stallName];
      
      const room = $('<div>')
        .addClass('stall-room')
        .addClass(isOccupied ? 'occupied' : 'vacant')
        .attr('data-stall-id', stall.stallID)
        .attr('data-stall-name', stallName);
      
      // Apply grid positioning if position mapping exists
      if (position) {
        room[0].style.setProperty('grid-row', position.gridRow, 'important');
        room[0].style.setProperty('grid-column', position.gridColumn, 'important');
      }
      
      room.html(`
          <div class="stall-room-name">${stall.stallNo}</div>
          <div class="stall-room-size">${stall.size}</div>
        `)
        .data('stall', stall)
        .data('level', level)
        .on('mouseenter', function(e) {
          currentHoveredStall[level] = $(this);
          // Clear any pending hide timeout
          if (window['stallCardTimeout' + level]) {
            clearTimeout(window['stallCardTimeout' + level]);
            window['stallCardTimeout' + level] = null;
          }
          showStallInfo($(this), level);
        })
        .on('mouseleave', function() {
          // Set timeout to hide card, but allow time to move to card
          window['stallCardTimeout' + level] = setTimeout(function() {
            hideStallInfo(level);
          }, 200);
        });
      
      grid.append(room);
    });
    
    // Handle card hover - keep visible when hovering over card
    const card = $(`#stallInfoCardHub${level}`);
    card.off('mouseenter mouseleave'); // Remove any existing handlers
    card.on('mouseenter', function() {
      // Clear hide timeout when mouse enters card
      if (window['stallCardTimeout' + level]) {
        clearTimeout(window['stallCardTimeout' + level]);
        window['stallCardTimeout' + level] = null;
      }
    }).on('mouseleave', function() {
      // Hide card immediately when mouse leaves the card
      hideStallInfo(level);
    });
  }
  
  function showStallInfo($room, level) {
    const stall = $room.data('stall');
    const card = $(`#stallInfoCardHub${level}`);
    const cardHeader = $(`#cardHeaderHub${level}`);
    const cardBody = $(`#cardBodyHub${level}`);
    const cardFooter = $(`#cardFooterHub${level}`);
    const wrapper = $room.closest('.floorplan-wrapper');
    
    const statusBadge = stall.isOccupied ? 'occupied-badge' : 'bg-label-danger';
    cardHeader.html(`<span class="badge ${statusBadge}">${stall.status}</span>`);
    
    let bodyHtml = `
      <div class="stall-info-item">
        <div class="stall-info-label">Stall Name</div>
        <div class="stall-info-value"><strong>${stall.stallNo}</strong></div>
      </div>
      <div class="stall-info-item">
        <div class="stall-info-label">Size (sq. m.)</div>
        <div class="stall-info-value">${stall.size}</div>
      </div>
    `;
    
    if (stall.isOccupied) {
      bodyHtml += `
        <div class="stall-info-item">
          <div class="stall-info-label">Rent By</div>
          <div class="stall-info-value">${stall.rentBy || '-'}</div>
        </div>
        <div class="stall-info-item">
          <div class="stall-info-label">Store Name</div>
          <div class="stall-info-value">${stall.storeName || '-'}</div>
        </div>
        <div class="stall-info-item">
          <div class="stall-info-label">Business Type</div>
          <div class="stall-info-value">${stall.businessType || '-'}</div>
        </div>
      `;
      cardFooter.html('');
    } else {
      if (stall.applicationDeadline) {
        if (stall.applicationOpen) {
          bodyHtml += `
            <div class="stall-info-item">
              <div class="stall-info-label">Application Availability</div>
              <div class="stall-info-value"><em style="color: #7F9267;">Tenant application is now open</em></div>
            </div>
          `;
          // Only show "Rent this stall" button if user doesn't have an active application or can reapply
          if (!stall.hasActiveApplication || stall.canReapply) {
            cardFooter.html(`<button class="btn btn-sm btn-primary w-100" onclick="rentStall(${stall.stallID})">Rent this stall</button>`);
          } else {
            cardFooter.html(`<div class="text-center text-muted small mt-2">You already have an active application for this stall.</div>`);
          }
        } else {
          cardFooter.html('');
        }
      } else {
        bodyHtml += `
          <div class="stall-info-item">
            <div class="stall-info-label">Application Availability</div>
            <div class="stall-info-value"><em style="color: #6c757d;">We're not accepting applications for this stall at the moment.</em></div>
          </div>
        `;
        cardFooter.html('');
      }
    }
    
    cardBody.html(bodyHtml);
    
    // Temporarily show card to measure it, but position it off-screen first
    card.css({
      visibility: 'hidden',
      display: 'block',
      position: 'absolute',
      left: '-9999px',
      top: '0'
    });
    card.addClass('show');
    
    // Force a reflow to ensure the card is measured
    card[0].offsetHeight;
    
    const cardWidth = card.outerWidth() || 280;
    const cardHeight = card.outerHeight() || 200;
    const wrapperWidth = wrapper.width();
    const wrapperHeight = wrapper.height();
    const wrapperPadding = 30; // Match padding of floorplan-wrapper
    
    // Get stall position relative to wrapper
    const roomRect = $room[0].getBoundingClientRect();
    const wrapperRect = wrapper[0].getBoundingClientRect();
    
    // Calculate stall position relative to wrapper
    const stallLeft = roomRect.left - wrapperRect.left;
    const stallTop = roomRect.top - wrapperRect.top;
    const stallRight = stallLeft + roomRect.width;
    const stallBottom = stallTop + roomRect.height;
    
    // Position card directly beside the stall with spacing (same for all stalls)
    const gap = 15; // Small space between stall and card
    
    // Get stall name to determine positioning
    const stallName = (stall.stallNo || '').toUpperCase().trim();
    
    // L1-4 and L2-6 should have card on RIGHT side (like L1-5 and L2-7 have on LEFT)
    // L1-5 and L2-7 should have card on LEFT side
    const forceRightSide = ['L1-4', 'L2-6'].includes(stallName);
    const forceLeftSide = ['L1-5', 'L2-7'].includes(stallName);
    
    let left, top;
    let isOnLeftSide = false;
    
    if (forceRightSide) {
      // Force card to RIGHT side for L1-4 and L2-6
      left = stallRight + gap;
      top = stallTop + (roomRect.height / 2) - (cardHeight / 2);
    } else if (forceLeftSide) {
      // Force card to LEFT side for L1-5 and L2-7
      left = stallLeft - cardWidth - gap;
      top = stallTop + (roomRect.height / 2) - (cardHeight / 2);
      isOnLeftSide = true;
    } else {
      // Default: try right side first
      left = stallRight + gap;
      top = stallTop + (roomRect.height / 2) - (cardHeight / 2);
      
      // Check if card would overlap with stall on right side
      const cardRight = left + cardWidth;
      const overlapsOnRight = (left < stallRight && cardRight > stallLeft);
      
      // If not enough space on right OR would overlap, use left side
      if (left + cardWidth > wrapperWidth - wrapperPadding || overlapsOnRight) {
        left = stallLeft - cardWidth - gap;
        isOnLeftSide = true;
      }
    }
    
    // Ensure card stays within wrapper bounds horizontally, but maintain gap from stall
    if (isOnLeftSide || forceLeftSide) {
      // On left side: ensure gap is maintained from stall
      const minLeft = stallLeft - cardWidth - gap;
      left = Math.max(wrapperPadding, Math.min(left, minLeft));
      // If bounds force it too close, ensure at least gap distance
      if (left + cardWidth >= stallLeft - gap) {
        left = stallLeft - cardWidth - gap;
      }
    } else {
      // On right side: ensure gap is maintained from stall
      const maxRight = stallRight + gap;
      left = Math.min(wrapperWidth - cardWidth - wrapperPadding, Math.max(left, maxRight));
      // If bounds force it too close, ensure at least gap distance
      if (left <= stallRight + gap) {
        left = stallRight + gap;
      }
    }
    
    // Ensure card stays within wrapper bounds vertically
    top = Math.max(wrapperPadding, Math.min(top, wrapperHeight - cardHeight - wrapperPadding));
    
    // Final overlap check - if card still overlaps with stall, force it to the side with more space
    const finalCardRight = left + cardWidth;
    const finalCardBottom = top + cardHeight;
    const overlapsHorizontally = (left < stallRight && finalCardRight > stallLeft);
    const overlapsVertically = (top < stallBottom && finalCardBottom > stallTop);
    
    if (overlapsHorizontally && overlapsVertically) {
      // Card overlaps - position on the side with more space
      const spaceOnRight = wrapperWidth - wrapperPadding - stallRight;
      const spaceOnLeft = stallLeft - wrapperPadding;
      
      if (spaceOnRight >= cardWidth + gap) {
        // Enough space on right, position there
        left = stallRight + gap;
      } else if (spaceOnLeft >= cardWidth + gap) {
        // Enough space on left, position there with gap
        left = stallLeft - cardWidth - gap;
      } else {
        // Not enough space on either side, position on the side with more space
        if (spaceOnRight > spaceOnLeft) {
          left = Math.max(wrapperPadding, stallRight + gap);
        } else {
          // On left side, ensure gap is maintained
          left = stallLeft - cardWidth - gap;
          if (left < wrapperPadding) {
            left = Math.max(wrapperPadding, stallLeft - cardWidth - gap);
          }
        }
      }
      
      // Final check: ensure gap is maintained
      if (forceRightSide) {
        // Force right side for L1-4 and L2-6
        left = stallRight + gap;
      } else if (forceLeftSide || (isOnLeftSide || left < stallRight)) {
        // Card is on left side, ensure gap
        if (left + cardWidth >= stallLeft - gap) {
          left = stallLeft - cardWidth - gap;
        }
      } else {
        // Card is on right side, ensure gap
        if (left <= stallRight + gap) {
          left = stallRight + gap;
        }
      }
      
      // Re-apply bounds but maintain gap
      if (forceRightSide) {
        // On right side - ensure gap maintained
        left = Math.min(wrapperWidth - cardWidth - wrapperPadding, Math.max(wrapperPadding, stallRight + gap));
        if (left <= stallRight + gap) {
          left = stallRight + gap;
        }
      } else if (forceLeftSide || left < stallRight) {
        // On left side
        const minLeft = stallLeft - cardWidth - gap;
        left = Math.max(wrapperPadding, Math.min(left, minLeft));
      } else {
        // On right side
        left = Math.max(wrapperPadding, Math.min(left, wrapperWidth - cardWidth - wrapperPadding));
      }
    }
    
    // Apply final position and make visible
    card.css({
      left: left + 'px',
      top: top + 'px',
      position: 'absolute',
      visibility: 'visible'
    });
  }
  
  function hideStallInfo(level) {
    currentHoveredStall[level] = null;
    const card = $(`#stallInfoCardHub${level}`);
    card.removeClass('show');
    card.css({
      display: 'none',
      visibility: 'hidden'
    });
  }
  
  window.rentStall = function(stallID) {
    window.location.href = `{{ route('tenants.applications.create') }}?stall=${stallID}`;
  };
});
</script>
@endpush

