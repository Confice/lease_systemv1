<!-- Feedback Detail Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="feedbackDetailDrawer" aria-labelledby="feedbackDetailDrawerLabel">
  <div class="offcanvas-header border-bottom bg-light">
    <div>
      <h5 class="offcanvas-title mb-1" id="feedbackDetailDrawerLabel">Feedback Details</h5>
      <small class="text-muted">Tenant submission snapshot</small>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <div class="d-flex justify-content-between flex-wrap gap-2 mb-4">
      <div>
        <h6 class="mb-1 text-muted">Tenant</h6>
        <p class="mb-0 fw-semibold" id="feedbackModalTenant">—</p>
        <small class="text-muted d-block" id="feedbackModalTenantContact"></small>
      </div>
      <div>
        <h6 class="mb-1 text-muted">Submitted</h6>
        <p class="mb-0 fw-semibold" id="feedbackModalSubmitted">—</p>
      </div>
    </div>

    <div class="row mb-4 g-3">
      <div class="col-md-6">
        <div class="border rounded p-3 h-100">
          <h6 class="text-muted mb-2">Contract</h6>
          <p class="mb-1"><strong>ID:</strong> <span id="feedbackModalContractId">—</span></p>
          <p class="mb-1"><strong>Start:</strong> <span id="feedbackModalContractStart">—</span></p>
          <p class="mb-0"><strong>End:</strong> <span id="feedbackModalContractEnd">—</span></p>
        </div>
      </div>
      <div class="col-md-6">
        <div class="border rounded p-3 h-100">
          <h6 class="text-muted mb-2">Stall & Marketplace</h6>
          <p class="mb-1"><strong>Stall:</strong> <span id="feedbackModalStall">—</span></p>
          <p class="mb-1"><strong>Marketplace:</strong> <span id="feedbackModalMarketplace">—</span></p>
          <p class="mb-0"><strong>Address:</strong> <span id="feedbackModalMarketplaceAddress">—</span></p>
        </div>
      </div>
    </div>

    <div id="feedbackModalSections"></div>

    <div class="mt-4">
      <h6 class="text-muted mb-2">Additional Comments</h6>
      <p class="mb-0" id="feedbackModalComments">No additional comments.</p>
    </div>
  </div>
</div>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const drawerEl = document.getElementById('feedbackDetailDrawer');
    if (!drawerEl) return;

    const sectionsContainer = document.getElementById('feedbackModalSections');

    const resetDrawer = () => {
      document.getElementById('feedbackModalTenant').textContent = '—';
      document.getElementById('feedbackModalTenantContact').textContent = '';
      document.getElementById('feedbackModalSubmitted').textContent = '—';
      document.getElementById('feedbackModalContractId').textContent = '—';
      document.getElementById('feedbackModalContractStart').textContent = '—';
      document.getElementById('feedbackModalContractEnd').textContent = '—';
      document.getElementById('feedbackModalStall').textContent = '—';
      document.getElementById('feedbackModalMarketplace').textContent = '—';
      document.getElementById('feedbackModalMarketplaceAddress').textContent = '—';
      document.getElementById('feedbackModalComments').textContent = 'No additional comments.';
      sectionsContainer.innerHTML = '';
    };

    drawerEl.addEventListener('hidden.bs.offcanvas', resetDrawer);

    drawerEl.addEventListener('show.bs.offcanvas', function (event) {
      const trigger = event.relatedTarget;
      if (!trigger) return;

      const payload = trigger.dataset.feedback ? JSON.parse(trigger.dataset.feedback) : null;
      if (!payload) return;

      document.getElementById('feedbackModalTenant').textContent = payload.tenant?.name || '—';
      const contactPieces = [];
      if (payload.tenant?.email) contactPieces.push(payload.tenant.email);
      if (payload.tenant?.contact) contactPieces.push(payload.tenant.contact);
      document.getElementById('feedbackModalTenantContact').textContent = contactPieces.join(' • ');
      document.getElementById('feedbackModalSubmitted').textContent = payload.submitted_at || '—';
      document.getElementById('feedbackModalContractId').textContent = payload.contract?.id || '—';
      document.getElementById('feedbackModalContractStart').textContent = payload.contract?.start_date || '—';
      document.getElementById('feedbackModalContractEnd').textContent = payload.contract?.end_date || '—';
      document.getElementById('feedbackModalStall').textContent = payload.stall?.stall_no || '—';
      document.getElementById('feedbackModalMarketplace').textContent = payload.stall?.marketplace || '—';
      document.getElementById('feedbackModalMarketplaceAddress').textContent = payload.stall?.marketplace_address || '—';
      document.getElementById('feedbackModalComments').textContent = payload.comments || 'No additional comments.';

      sectionsContainer.innerHTML = '';
      Object.entries(payload.sections || {}).forEach(([sectionName, entries]) => {
        if (!entries || !entries.length) return;

        const card = document.createElement('div');
        card.className = 'card mb-3';
        const body = document.createElement('div');
        body.className = 'card-body';
        body.innerHTML = `<h6 class="card-title mb-3">${sectionName}</h6>`;

        entries.forEach(item => {
          const row = document.createElement('div');
          row.className = 'd-flex justify-content-between border-bottom py-2';
          row.innerHTML = `
            <span class="me-3">${item.label}</span>
            <span class="fw-semibold">${item.value ?? '—'} / 5</span>
          `;
          body.appendChild(row);
        });

        card.appendChild(body);
        sectionsContainer.appendChild(card);
      });
    });
  });
</script>
@endpush

