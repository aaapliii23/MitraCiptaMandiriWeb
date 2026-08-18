    <!-- Scripts -->
    <script>
        // Inject PHP data to JS
        const serverClassData = <?php 
            $jsClasses = [];
            foreach($classItems as $c) {
                $jsClasses[$c['name']] = [
                    'title' => $c['name'],
                    'desc' => $c['description'],
                    'image' => $c['image'],
                    'features' => json_decode($c['features'], true) ?: []
                ];
            }
            echo json_encode($jsClasses);
        ?>;
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            if (!form.reportValidity()) return;

            const btn = document.getElementById('btnCheckoutLanjut');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Menyimpan...';

            fetch('booking/submit_booking.php', {
                method: 'POST',
                body: new FormData(form)
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = 'Lanjutkan ke Pembayaran';
                if (data.status === 'success') {
                    const classId = document.getElementById('bookingClassId').value;
                    const cls = (window.mcmClassDetails || {})[classId] || null;
                    document.getElementById('checkoutClassId').value = classId;
                    document.getElementById('checkoutClassName').textContent = cls ? cls.name : document.getElementById('kelas').value;
                    document.getElementById('checkoutClassPrice').textContent = 'Rp ' + parseInt(cls ? cls.price : 0).toLocaleString('id-ID');
                    document.getElementById('checkoutTotalPrice').textContent = 'Rp ' + parseInt(cls ? cls.price : 0).toLocaleString('id-ID');
                    document.querySelector('#checkoutForm input[name="customer_name"]').value = document.getElementById('name').value;
                    document.querySelector('#checkoutForm input[name="customer_phone"]').value = document.getElementById('whatsapp').value;
                    document.querySelector('#checkoutForm input[name="customer_email"]').value = document.getElementById('email').value;

                    const bookingModalEl = document.getElementById('bookingModal');
                    const checkoutModalEl = document.getElementById('checkoutModal');
                    if (bookingModalEl) bootstrap.Modal.getOrCreateInstance(bookingModalEl).hide();
                    if (checkoutModalEl) bootstrap.Modal.getOrCreateInstance(checkoutModalEl).show();
                } else {
                    Swal.fire('Gagal', data.message, 'error');
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerHTML = 'Lanjutkan ke Pembayaran';
                Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
            });
        });
    </script>
    <script src="assets/js/script.js?v=<?php echo time(); ?>"></script>
