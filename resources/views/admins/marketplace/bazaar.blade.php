@extends('layouts.admin_app')

@section('title', 'Bazaar - Marketplace Map')

@push('styles')
<style>
  .page-header-center {
    text-align: center;
    margin-bottom: 30px;
  }
  
  .page-header-center h2 {
    font-weight: bold;
    margin: 0;
  }
  
  .floorplan-wrapper {
    background: #EFEFEA;
    padding: 40px;
    border-radius: 8px;
    border: 2px solid #7F9267;
    margin-bottom: 30px;
    position: relative;
  }
  
  .level-header {
    text-align: center;
    font-weight: bold;
    margin-bottom: 20px;
    font-size: 1.3rem;
    color: #000000;
  }
  
  .floorplan-grid {
    display: grid;
    gap: 20px;
    width: 100%;
    max-width: 1400px;
    margin: 0 auto;
  }
  
  .floorplan-grid.bazaar-layout {
    grid-template-columns: repeat(6, 1fr);
    grid-auto-rows: minmax(150px, auto);
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
  
  /* Match status pill colors: bg-label-primary for occupied */
  .stall-room.occupied {
    background-color: #EFEFEA !important;
    border-color: rgba(127, 146, 103, 0.2) !important;
    color: #7F9267;
  }
  
  .stall-room.occupied::before {
    background: rgba(127, 146, 103, 0.2);
  }
  
  /* Match status pill colors: bg-label-danger for vacant */
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
    color: #7F9267;
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
    color: #7F9267;
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
    margin-bottom: 25px;
    padding: 20px;
    background: #FEFEFE;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    justify-content: center;
    border: 1px solid #7F9267;
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
    background-color: #EFEFEA;
    border-color: rgba(127, 146, 103, 0.2);
  }
  
  .legend-color.vacant {
    background-color: #ffe0db;
    border-color: #ffb2a5;
  }
</style>
@endpush

@section('content')
<div class="mb-3">
  <button type="button" class="btn btn-outline-secondary" onclick="window.history.back()">
    <i class="bx bx-arrow-back me-1"></i> Back
  </button>
</div>
<div class="page-header-center">
  <h2>Commercial Spaces at Bazaar</h2>
</div>

<div class="card">
  <div class="card-body">
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
    
    <!-- Level 1 Floorplan -->
    <div class="level-header">Level 1</div>
    <div class="floorplan-wrapper">
      <div class="floorplan-grid bazaar-layout" id="floorplanGrid1">
        <!-- Stalls will be dynamically inserted here -->
      </div>
      <div class="stall-info-card" id="stallInfoCard1">
        <div class="stall-info-card-header" id="cardHeader1"></div>
        <div class="stall-info-card-body" id="cardBody1"></div>
        <div class="stall-info-card-footer" id="cardFooter1"></div>
      </div>
    </div>
    
    <!-- Level 2 Floorplan -->
    <div class="level-header">Level 2</div>
    <div class="floorplan-wrapper">
      <div class="floorplan-grid bazaar-layout" id="floorplanGrid2">
        <!-- Stalls will be dynamically inserted here -->
      </div>
      <div class="stall-info-card" id="stallInfoCard2">
        <div class="stall-info-card-header" id="cardHeader2"></div>
        <div class="stall-info-card-body" id="cardBody2"></div>
        <div class="stall-info-card-footer" id="cardFooter2"></div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(function(){
  const marketplaceName = 'Bazaar';
  let stallsData = [];
  let currentHoveredStall = {1: null, 2: null};
  
  // Stall level mapping
  const stallLevels = {
    1: 1, 2: 1, 3: 1,
    4: 2, 5: 2, 6: 2,
  };
  
  // Load stalls data
  $.get('/marketplace/stalls', { marketplace: marketplaceName }, function(response) {
    stallsData = response.data || [];
    renderStalls();
  });
  
  function renderStalls() {
    renderLevel(1);
    renderLevel(2);
  }
  
  function renderLevel(level) {
    const grid = $(`#floorplanGrid${level}`);
    grid.empty();
    
    stallsData.forEach(function(stall) {
      const stallNum = stall.stallNumber || stall.stallID;
      let stallLevel = stallLevels[stallNum];
      
      // If no level mapping found, try to determine from stall name
      if (!stallLevel) {
        const stallName = (stall.stallNo || '').toUpperCase().trim();
        if (stallName.startsWith('L1-') || stallName.startsWith('L1')) {
          stallLevel = 1;
        } else if (stallName.startsWith('L2-') || stallName.startsWith('L2')) {
          stallLevel = 2;
        } else {
          // Default to level 1 if no pattern matches
          stallLevel = 1;
        }
      }
      
      if (stallLevel !== level) {
        return;
      }
      
      const isOccupied = stall.isOccupied;
      
      const room = $('<div>')
        .addClass('stall-room')
        .addClass(isOccupied ? 'occupied' : 'vacant')
        .attr('data-stall-id', stall.stallID)
        .attr('data-stall-number', stallNum)
        .html(`
          <div class="stall-room-name">${stall.stallNo}</div>
          <div class="stall-room-size">${stall.size}</div>
        `)
        .data('stall', stall)
        .data('level', level)
        .on('mouseenter', function(e) {
          currentHoveredStall[level] = $(this);
          showStallInfo($(this), level);
        })
        .on('mouseleave', function() {
          setTimeout(function() {
            if (!currentHoveredStall[level] || !currentHoveredStall[level].is(':hover') && !$(`#stallInfoCard${level}:hover`).length) {
              hideStallInfo(level);
            }
          }, 100);
        });
      
      grid.append(room);
    });
    
    $(`#stallInfoCard${level}`).on('mouseenter', function() {
      // Keep it visible
    }).on('mouseleave', function() {
      hideStallInfo(level);
    });
  }
  
  function showStallInfo($room, level) {
    const stall = $room.data('stall');
    const card = $(`#stallInfoCard${level}`);
    const cardHeader = $(`#cardHeader${level}`);
    const cardBody = $(`#cardBody${level}`);
    const cardFooter = $(`#cardFooter${level}`);
    const roomOffset = $room.offset();
    const roomWidth = $room.outerWidth();
    const roomHeight = $room.outerHeight();
    const wrapper = $room.closest('.floorplan-wrapper');
    const wrapperOffset = wrapper.offset();
    
    // Header with status
    const statusBadge = stall.isOccupied ? 'bg-label-primary' : 'bg-label-danger';
    cardHeader.html(`<span class="badge ${statusBadge}">${stall.status}</span>`);
    
    // Body content
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
          cardFooter.html(`<button class="btn btn-sm btn-primary w-100 view-applications-btn" data-stall-id="${stall.stallID}">View Applications</button>`);
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
    card.addClass('show');
    
    // Position card near the stall
    const cardWidth = 280;
    let left = roomOffset.left - wrapperOffset.left + roomWidth + 15;
    let top = roomOffset.top - wrapperOffset.top;
    
    if (left + cardWidth > wrapper.width()) {
      left = roomOffset.left - wrapperOffset.left - cardWidth - 15;
    }
    
    const cardHeight = card.outerHeight() || 200;
    top = top + (roomHeight / 2) - (cardHeight / 2);
    
    if (top < 0) top = 10;
    if (top + cardHeight > wrapper.height()) {
      top = wrapper.height() - cardHeight - 10;
    }
    
    card.css({
      left: left + 'px',
      top: top + 'px',
      position: 'absolute'
    });
  }
  
  function hideStallInfo(level) {
    currentHoveredStall[level] = null;
    $(`#stallInfoCard${level}`).removeClass('show');
  }
  
  // Handle View Applications button clicks using event delegation
  $(document).on('click', '.view-applications-btn', function(e) {
    e.preventDefault();
    const stallID = $(this).data('stall-id');
    console.log('View Applications clicked for stallID:', stallID);
    if (stallID) {
      window.location.href = "{{ url('/tenants/prospective') }}/" + stallID + "/applications";
    } else {
      console.error('Stall ID not found');
    }
  });
});
</script>
@endpush
