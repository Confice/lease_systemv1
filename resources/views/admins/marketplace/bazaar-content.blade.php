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
  
  .floorplan-wrapper {
    background: #EFEFEA;
    padding: 40px;
    border-radius: 8px;
    border: 2px solid #7F9267;
    margin-bottom: 0;
    margin-top: 1.5rem;
    position: relative;
  }
  
  .floorplan-wrapper:last-child {
    margin-bottom: 0;
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
  
  .floorplan-grid {
    display: grid;
    gap: 20px;
    width: 100%;
    max-width: 1400px;
    margin: 0 auto;
  }
  
  .floorplan-grid.bazaar-layout.level1 {
    display: grid !important;
    grid-template-columns: repeat(24, 1fr) !important;
    grid-template-rows: minmax(150px, auto) 30px 30px minmax(150px, auto) !important;
    gap: 15px;
    grid-auto-flow: row;
  }
  
  /* Walkway row - no stalls, just spacing */
  .floorplan-grid.bazaar-layout.level1 .walkway {
    grid-column: 1 / -1;
    grid-row: 2;
    background: transparent;
    border: none;
    min-height: 30px;
  }
  
  /* Center gap - no service block displayed, just empty space */
  
  .floorplan-grid.bazaar-layout.level2 {
    display: grid !important;
    grid-template-columns: repeat(24, 1fr) !important;
    grid-template-rows: minmax(150px, auto) 30px minmax(150px, auto) !important;
    gap: 15px;
    grid-auto-flow: row;
  }
  
  /* Walkway row for Level 2 - no stalls, just spacing */
  .floorplan-grid.bazaar-layout.level2 .walkway {
    grid-column: 1 / -1;
    grid-row: 2;
    background: transparent;
    border: none;
    min-height: 30px;
  }
  
  .stall-room {
    grid-row: unset !important;
    grid-column: unset !important;
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
</style>
@endpush

<div class="page-header-center">
  <h2>Commercial Spaces at Bazaar</h2>
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

<!-- Level 1A Floorplan -->
<div class="level-header">Level 1A</div>
<div class="floorplan-wrapper">
  <div class="floorplan-grid bazaar-layout level1" id="floorplanGridBazaar1">
    <!-- Stalls will be dynamically inserted here -->
  </div>
  <div class="stall-info-card" id="stallInfoCardBazaar1">
    <div class="stall-info-card-header" id="cardHeaderBazaar1"></div>
    <div class="stall-info-card-body" id="cardBodyBazaar1"></div>
    <div class="stall-info-card-footer" id="cardFooterBazaar1"></div>
  </div>
</div>

<!-- Level 1B Floorplan -->
<div class="level-header">Level 1B</div>
<div class="floorplan-wrapper">
  <div class="floorplan-grid bazaar-layout level2" id="floorplanGridBazaar2">
    <!-- Stalls will be dynamically inserted here -->
  </div>
  <div class="stall-info-card" id="stallInfoCardBazaar2">
    <div class="stall-info-card-header" id="cardHeaderBazaar2"></div>
    <div class="stall-info-card-body" id="cardBodyBazaar2"></div>
    <div class="stall-info-card-footer" id="cardFooterBazaar2"></div>
  </div>
</div>

@push('scripts')
<script>
$(function(){
  const marketplaceName = 'Bazaar';
  let stallsData = [];
  let currentOpenLevel = null; // which level has the stall info card visible (1 or 2)
  
  // Map stall names to levels and positions
  // Level 1: Two long horizontal rows separated by a central walkway
  // Top Row: Left Cluster (L1A-F1 to L1A-CLS) - 5 stalls from left to right
  // Level 2: All other stalls (default location for now)
  
  // Grid positions for Level 1 stalls - Top Row:
  // Left Cluster: L1A-F1 (widest), F2, F3, F4, CLS (wider than before)
  // Center Gap: Smaller gap (columns 9-10)
  // Right Cluster: L1A-S1 (widest), S2, S3 (same size, moved left to fill gap)
  // Support both naming patterns: "L1A-F1" or "F1", "L1A-S1" or "S1", etc.
  const level1Positions = {
    // Left Cluster - Sized based on square footage: F1(47) > F2(44) > F3(37)=F4(37) > CLS(27)
    // Left cluster occupies columns 1-12, leaving space for gap and right cluster
    'L1A-F1': { gridRow: '1', gridColumn: '1 / 5', level: 1 },  // 47 sq ft - Largest in left cluster, spans 4 columns
    'F1': { gridRow: '1', gridColumn: '1 / 5', level: 1 },      // Alternative name for L1A-F1
    'L1A-F2': { gridRow: '1', gridColumn: '5 / 8', level: 1 },  // 44 sq ft - Slightly smaller than F1, spans 3 columns
    'F2': { gridRow: '1', gridColumn: '5 / 8', level: 1 },      // Alternative name for L1A-F2
    'L1A-F3': { gridRow: '1', gridColumn: '8 / 10', level: 1 },  // 37 sq ft - EXACTLY same size as F4, spans 2 columns, same height
    'F3': { gridRow: '1', gridColumn: '8 / 10', level: 1 },      // Alternative name for L1A-F3
    'L1A-F4': { gridRow: '1', gridColumn: '10 / 12', level: 1 },  // 37 sq ft - EXACTLY same size as F3, spans 2 columns, same height
    'F4': { gridRow: '1', gridColumn: '10 / 12', level: 1 },      // Alternative name for L1A-F4
    'L1A-CLS': { gridRow: '1', gridColumn: '12 / 14', level: 1 },  // 27 sq ft - Smallest, spans 2 columns
    'CLS': { gridRow: '1', gridColumn: '12 / 14', level: 1 },      // Alternative name for L1A-CLS
    // Right Cluster - Sized based on square footage: S1(93) > S2(66)=S3(66)
    // S1, S2, S3 are taller (span 2 rows) and S1 is the largest
    // Right cluster starts after gap, S1 should be largest (93 sq ft), S2 and S3 equal (66 sq ft each)
    'L1A-S1': { gridRow: '1 / 3', gridColumn: '15 / 19', level: 1 },  // 93 sq ft - Largest stall, right cluster, spans 2 rows, 4 columns (space after CLS)
    'S1': { gridRow: '1 / 3', gridColumn: '15 / 19', level: 1 },      // Alternative name for L1A-S1
    'L1A-S2': { gridRow: '1 / 3', gridColumn: '19 / 22', level: 1 },  // 66 sq ft - Same size as S3, right cluster, spans 2 rows, 3 columns (wider)
    'S2': { gridRow: '1 / 3', gridColumn: '19 / 22', level: 1 },      // Alternative name for L1A-S2
    'L1A-S3': { gridRow: '1 / 3', gridColumn: '22 / -1', level: 1 },  // 66 sq ft - Same size as S2, extends to absolute end, spans 2 rows, 3 columns (wider)
    'S3': { gridRow: '1 / 3', gridColumn: '22 / -1', level: 1 },      // Alternative name for L1A-S3
    // Bottom Row (Row 4) - Sized based on square footage: S4(69)=S5(69)=S6(69) > F5(50) > F6(34)=F7(34)=F8(34)=F9(34)=S7(34)
    // S4-S6 are largest, F5 is smaller than S4-S6, F6-F9 and S7 are same size (smallest)
    'L1A-F5': { gridRow: '4', gridColumn: '3 / 7', level: 1 },  // 50 sq ft - Smaller than S4-S6, spans 4 columns
    'F5': { gridRow: '4', gridColumn: '3 / 7', level: 1 },       // Alternative name for L1A-F5
    'L1A-F6': { gridRow: '4', gridColumn: '7 / 8', level: 1 },  // 34 sq ft - Same size as F7, F8, F9, S7, spans 1 column
    'F6': { gridRow: '4', gridColumn: '7 / 8', level: 1 },       // Alternative name for L1A-F6
    'L1A-F7': { gridRow: '4', gridColumn: '8 / 9', level: 1 },  // 34 sq ft - Same size as F6, F8, F9, S7, spans 1 column
    'F7': { gridRow: '4', gridColumn: '8 / 9', level: 1 },       // Alternative name for L1A-F7
    'L1A-F8': { gridRow: '4', gridColumn: '9 / 10', level: 1 },  // 34 sq ft - Same size as F6, F7, F9, S7, spans 1 column
    'F8': { gridRow: '4', gridColumn: '9 / 10', level: 1 },      // Alternative name for L1A-F8
    'L1A-F9': { gridRow: '4', gridColumn: '10 / 11', level: 1 }, // 34 sq ft - Same size as F6, F7, F8, S7, spans 1 column
    'F9': { gridRow: '4', gridColumn: '10 / 11', level: 1 },     // Alternative name for L1A-F9
    'L1A-S4': { gridRow: '4', gridColumn: '11 / 15', level: 1 }, // 69 sq ft - Same size as S5, S6, spans 4 columns (bigger width)
    'S4': { gridRow: '4', gridColumn: '11 / 15', level: 1 },    // Alternative name for L1A-S4
    'L1A-S5': { gridRow: '4', gridColumn: '15 / 19', level: 1 }, // 69 sq ft - Same size as S4, S6, spans 4 columns (bigger width)
    'S5': { gridRow: '4', gridColumn: '15 / 19', level: 1 },    // Alternative name for L1A-S5
    'L1A-S6': { gridRow: '4', gridColumn: '19 / 23', level: 1 }, // 69 sq ft - Same size as S4, S5, spans 4 columns (bigger width)
    'S6': { gridRow: '4', gridColumn: '19 / 23', level: 1 },   // Alternative name for L1A-S6
    'L1A-S7': { gridRow: '4', gridColumn: '23 / -1', level: 1 }, // 34 sq ft - Same size as F6-F9, extends to end, no gap after
    'S7': { gridRow: '4', gridColumn: '23 / -1', level: 1 }     // Alternative name for L1A-S7
  };
  
  // Level 2 positions - Top Row: CL1 (88) > CL2(62)=CL3(62)=CL4(62)=CL5(62)=CL6(62)
  // Two long horizontal rows separated by a central walkway
  const level2Positions = {
    // Top Row (Row 1) - CL1 is widest, space after CL1, CL2-CL6 are equal medium stalls, space after CL6
    'L1B-CL1': { gridRow: '1', gridColumn: '1 / 6', level: 2 },  // 88 sq ft - Widest, leftmost, spans 5 columns
    'CL1': { gridRow: '1', gridColumn: '1 / 6', level: 2 },      // Alternative name for L1B-CL1
    'L1B-CL2': { gridRow: '1', gridColumn: '7 / 10', level: 2 }, // 62 sq ft - Same size as CL3-CL6, spans 3 columns
    'CL2': { gridRow: '1', gridColumn: '7 / 10', level: 2 },      // Alternative name for L1B-CL2
    'L1B-CL3': { gridRow: '1', gridColumn: '10 / 13', level: 2 }, // 62 sq ft - Same size as CL2, CL4-CL6, spans 3 columns
    'CL3': { gridRow: '1', gridColumn: '10 / 13', level: 2 },     // Alternative name for L1B-CL3
    'L1B-CL4': { gridRow: '1', gridColumn: '13 / 16', level: 2 }, // 62 sq ft - Same size as CL2, CL3, CL5, CL6, spans 3 columns
    'CL4': { gridRow: '1', gridColumn: '13 / 16', level: 2 },      // Alternative name for L1B-CL4
    'L1B-CL5': { gridRow: '1', gridColumn: '16 / 19', level: 2 }, // 62 sq ft - Same size as CL2-CL4, CL6, spans 3 columns
    'CL5': { gridRow: '1', gridColumn: '16 / 19', level: 2 },      // Alternative name for L1B-CL5
    'L1B-CL6': { gridRow: '1', gridColumn: '19 / 22', level: 2 }, // 62 sq ft - Same size as CL2-CL5, spans 3 columns
    'CL6': { gridRow: '1', gridColumn: '19 / 22', level: 2 },       // Alternative name for L1B-CL6
    // Bottom Row (Row 3) - CU1(89) > CU2(62)=CU3(62)=CU4(62)=CU5(62)=T1(62)=T2(62) same size as CL2-CL6
    'L1B-CU1': { gridRow: '3', gridColumn: '1 / 6', level: 2 },  // 89 sq ft - Widest, leftmost, spans 5 columns
    'CU1': { gridRow: '3', gridColumn: '1 / 6', level: 2 },       // Alternative name for L1B-CU1
    'L1B-CU2': { gridRow: '3', gridColumn: '6 / 9', level: 2 },  // 62 sq ft - Same size as CL2-CL6, CU3-CU5, T1-T2, spans 3 columns
    'CU2': { gridRow: '3', gridColumn: '6 / 9', level: 2 },      // Alternative name for L1B-CU2
    'L1B-CU3': { gridRow: '3', gridColumn: '9 / 12', level: 2 }, // 62 sq ft - Same size as CL2-CL6, CU2, CU4-CU5, T1-T2, spans 3 columns
    'CU3': { gridRow: '3', gridColumn: '9 / 12', level: 2 },     // Alternative name for L1B-CU3
    'L1B-CU4': { gridRow: '3', gridColumn: '12 / 15', level: 2 }, // 62 sq ft - Same size as CL2-CL6, CU2-CU3, CU5, T1-T2, spans 3 columns
    'CU4': { gridRow: '3', gridColumn: '12 / 15', level: 2 },     // Alternative name for L1B-CU4
    'L1B-CU5': { gridRow: '3', gridColumn: '15 / 18', level: 2 }, // 62 sq ft - Same size as CL2-CL6, CU2-CU4, T1-T2, spans 3 columns
    'CU5': { gridRow: '3', gridColumn: '15 / 18', level: 2 },    // Alternative name for L1B-CU5
    'L1B-T1': { gridRow: '3', gridColumn: '18 / 21', level: 2 },  // 62 sq ft - Same size as CL2-CL6, CU2-CU5, T2, spans 3 columns
    'T1': { gridRow: '3', gridColumn: '18 / 21', level: 2 },      // Alternative name for L1B-T1
    'L1B-T2': { gridRow: '3', gridColumn: '21 / 24', level: 2 },  // 62 sq ft - Same size as CL2-CL6, CU2-CU5, T1, spans 3 columns
    'T2': { gridRow: '3', gridColumn: '21 / 24', level: 2 }       // Alternative name for L1B-T2
  };
  
  // Combined positions map
  const allPositions = { ...level1Positions, ...level2Positions };
  
  $.ajax({
    url: '/marketplace/stalls',
    method: 'GET',
    data: { marketplace: marketplaceName },
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function(response) {
      stallsData = response.data || [];
      console.log('All stalls data:', stallsData);
      console.log('All stall names:', stallsData.map(s => s.stallNo));
      if (response.error) {
        console.error('Server error:', response.error);
        alert('Error: ' + response.error);
      } else {
        renderStalls();
      }
    },
    error: function(xhr, status, error) {
      console.error('Error loading stalls:', status, error);
      console.error('Response:', xhr.responseText);
      console.error('Status code:', xhr.status);
      let errorMsg = 'Failed to load stalls. ';
      if (xhr.responseJSON && xhr.responseJSON.error) {
        errorMsg += xhr.responseJSON.error;
      } else if (xhr.status === 401) {
        errorMsg += 'Please log in again.';
      } else if (xhr.status === 403) {
        errorMsg += 'Access denied.';
      } else {
        errorMsg += 'Please check the console for details.';
      }
      alert(errorMsg);
    }
  });
  
  function renderStalls() {
    renderLevel(1);
    renderLevel(2);
  }
  
  function renderLevel(level) {
    const grid = $(`#floorplanGridBazaar${level}`);
    grid.empty();
    grid.addClass(`level${level}`);
    
    // For Level 1 and Level 2, add walkway row separator
    if (level === 1 || level === 2) {
      // Add walkway separator
      const walkway = $('<div>').addClass('walkway').css({
        'grid-column': '1 / -1',
        'grid-row': '2',
        'background': 'transparent',
        'border': 'none',
        'min-height': '30px'
      });
      grid.append(walkway);
      
      // Center gap is left empty (no service block displayed)
    }
    
    // Filter stalls by level
    // Level 1: Only stalls with specific position mappings (Left Cluster: L1A-F1, F2, F3, F4, CLS | Right Cluster: L1A-S1, S2, S3)
    // Level 2: All other stalls (default location for now)
    const levelStalls = stallsData.filter(function(stall) {
      const stallName = (stall.stallNo || '').toUpperCase().trim();
      const position = allPositions[stallName];
      
      if (level === 1) {
        // Level 1: Only show stalls with specific position mappings (L1A-F1, F2, F3, F4, CLS)
        return position && position.level === 1;
      } else if (level === 2) {
        // Level 2: Show all stalls that are NOT in Level 1 (all other stalls)
        // This includes stalls without position mappings and any future Level 2 stalls
        if (!position) {
          return true; // No position mapping = default to Level 2
        }
        return position.level !== 1; // Has position but not Level 1 = show in Level 2
      }
      
      return false;
    });
    
    levelStalls.forEach(function(stall) {
      const stallName = (stall.stallNo || '').toUpperCase().trim();
      const isOccupied = stall.isOccupied;
      
      // Get position mapping by stall name
      const position = allPositions[stallName];
      
      console.log(`Stall: ${stall.stallNo}, Stall Name: ${stallName}, Level: ${level}, Position:`, position);
      
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
      // If no position mapping, let CSS grid auto-place the stall
      
      room.html(`
          <div class="stall-room-name">${stall.stallNo}</div>
          <div class="stall-room-size">${stall.size}</div>
        `)
        .data('stall', stall)
        .data('level', level)
        .on('click', function(e) {
          e.stopPropagation();
          if (currentOpenLevel !== null && currentOpenLevel !== level) {
            hideStallInfo(currentOpenLevel);
          }
          currentOpenLevel = level;
          showStallInfo($(this), level);
        });
      
      grid.append(room);
    });
  }
  
  // Close card when clicking outside any stall or the popup card
  $(document).on('click', function(e) {
    if ($(e.target).closest('.stall-room').length || $(e.target).closest('.stall-info-card').length) {
      return;
    }
    if (currentOpenLevel !== null) {
      hideStallInfo(currentOpenLevel);
      currentOpenLevel = null;
    }
  });
  
  $(document).on('click', '.stall-info-card', function(e) {
    e.stopPropagation();
  });
  
  function showStallInfo($room, level) {
    const stall = $room.data('stall');
    const card = $(`#stallInfoCardBazaar${level}`);
    const cardHeader = $(`#cardHeaderBazaar${level}`);
    const cardBody = $(`#cardBodyBazaar${level}`);
    const cardFooter = $(`#cardFooterBazaar${level}`);
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
          cardFooter.html(`<button class="btn btn-sm btn-primary w-100 view-applications-btn" data-stall-id="${stall.stallID}">View Applications</button>`);
        } else {
          cardFooter.html('');
        }
      } else {
        bodyHtml += `
          <div class="stall-info-item">
            <div class="stall-info-label">Application</div>
            <div class="stall-info-value"><em style="color: #7F9267;">Vacant stall</em></div>
          </div>
        `;
        cardFooter.html(`<button class="btn btn-sm btn-primary w-100 view-applications-btn" data-stall-id="${stall.stallID}">View Applications</button>`);
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
    
    // Position card directly beside the stall (right side by default)
    const gap = 15; // Small space between stall and card
    let left = stallRight + gap;
    let top = stallTop + (roomRect.height / 2) - (cardHeight / 2); // Vertically centered
    
    // If card doesn't fit on right side, place on left side
    if (left + cardWidth > wrapperWidth - wrapperPadding) {
      left = stallLeft - cardWidth - gap;
    }
    
    // Ensure card stays within wrapper bounds horizontally
    left = Math.max(wrapperPadding, Math.min(left, wrapperWidth - cardWidth - wrapperPadding));
    
    // Ensure card stays within wrapper bounds vertically
    top = Math.max(wrapperPadding, Math.min(top, wrapperHeight - cardHeight - wrapperPadding));
    
    // Apply final position and make visible
    card.css({
      left: left + 'px',
      top: top + 'px',
      position: 'absolute',
      visibility: 'visible'
    });
  }
  
  function hideStallInfo(level) {
    const card = $(`#stallInfoCardBazaar${level}`);
    card.removeClass('show');
    card.css({
      'display': 'none',
      'visibility': 'hidden'
    });
  }
  
  // Handle View Applications button clicks using event delegation
  $(document).on('click', '.view-applications-btn', function(e) {
    e.preventDefault();
    const stallID = $(this).data('stall-id');
    console.log('View Applications clicked for stallID:', stallID);
    if (stallID) {
      window.location.href = "{{ url('/tenants/prospective') }}/" + stallID + "/applications?from=marketplace";
    } else {
      console.error('Stall ID not found');
    }
  });
});
</script>
@endpush

