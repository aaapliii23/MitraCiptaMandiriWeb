<script>
function reinitBootstrapModals() {
    // Re-attach data-bs-toggle modal triggers after AJAX content swap
    document.querySelectorAll('[data-bs-toggle="modal"]').forEach(btn => {
        if (btn.dataset.modalBound) return;
        btn.dataset.modalBound = 'true';
        btn.addEventListener('click', function() {
            const target = this.getAttribute('data-bs-target');
            if (target) showModal(target.replace('#', ''));
        });
    });

    // Re-attach updateStatus modal events
    const updateStatusModal = document.getElementById('updateStatusModal');
    if (updateStatusModal && !updateStatusModal.dataset.bound) {
        updateStatusModal.dataset.bound = 'true';
        updateStatusModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (!button) return;
            document.getElementById('modalOrderId').value = button.getAttribute('data-id');
            document.getElementById('modalParticipantName').textContent = button.getAttribute('data-name');
            document.getElementById('modalOrderStatus').value = button.getAttribute('data-status');
        });
    }

    // Re-attach detail pesanan modal events
    const detailPesananModal = document.getElementById('detailPesananModal');
    if (detailPesananModal && !detailPesananModal.dataset.bound) {
        detailPesananModal.dataset.bound = 'true';
        detailPesananModal.addEventListener('show.bs.modal', function(event) {
            const btn = event.relatedTarget;
            if (!btn) return;
            const map = {
                'detailOrderNum': 'data-order', 'detailName': 'data-name',
                'detailPhone': 'data-phone', 'detailEmail': 'data-email',
                'detailInstansi': 'data-instansi', 'detailAlamat': 'data-alamat',
                'detailKelas': 'data-kelas', 'detailHarga': 'data-harga'
            };
            for (const [id, attr] of Object.entries(map)) {
                const el = document.getElementById(id);
                if (el) el.textContent = btn.getAttribute(attr) || '-';
            }
        });
    }
}

function showModal(id) {
    const el = document.getElementById(id);
    if (el) {
        const modal = bootstrap.Modal.getOrCreateInstance(el);
        modal.show();
    }
}
</script>
