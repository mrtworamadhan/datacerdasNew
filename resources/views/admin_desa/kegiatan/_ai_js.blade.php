<script>
$(document).ready(function() {
    // ... (script untuk dropdown dinamis tidak berubah, biarkan di sini) ...

    // Event listener untuk semua tombol .ai-helper-btn
    $('.ai-helper-btn').on('click', function() {
        const button = $(this);
        const targetTextarea = $(button.data('target'));
        const section = button.data('section');

        // =================================================================
        // === PENYEMPURNAAN UTAMA: Ambil semua data dengan jQuery ===
        // =================================================================
        const context = $('#nama_kegiatan').val();
        const penyelenggaraType = $('#penyelenggara_type').val();
        const penyelenggaraId = $('#penyelenggara_id').val();
        const penyelenggaraNama = (penyelenggaraId) ? $('#penyelenggara_id option:selected').text().trim() : '';

        const dataToSend = {
            _token: "{{ csrf_token() }}",
            context: context,
            section: section,
            penyelenggara_type: penyelenggaraType,
            penyelenggara_id: penyelenggaraId,
            penyelenggara_nama: penyelenggaraNama,
            nama_desa: '{{ auth()->user()->desa->nama_desa ?? "Nama Desa" }}',
            lokasi_kegiatan: $('input[name="lokasi_kegiatan"]').val(),
            anggaran: $('input[name="anggaran_biaya"]').val(),
            tanggal: $('input[name="tanggal_kegiatan"]').val()
        };
        // =================================================================

        if (!context) {
            alert('Silakan isi "Nama Kegiatan" terlebih dahulu sebagai konteks.');
            return;
        }

        button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');

        // Kirim request ke AI menggunakan fetch API (kodemu sudah bagus)
        fetch('{{ route("ai.generate.text") }}', { // Pastikan nama route sudah benar
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify(dataToSend)
        })
        .then(response => {
            if (!response.ok) {
                // Jika server merespons dengan error (4xx, 5xx), baca pesannya
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(result => {
            // Jika sukses, tambahkan teks ke textarea
            let currentText = targetTextarea.val();
            targetTextarea.val(currentText + result.text);
        })
        .catch(error => {
            console.error('Final Error:', error);
            alert('Terjadi kesalahan: ' + (error.message || 'Gagal terhubung.'));
        })
        .finally(() => {
            // Kembalikan tombol ke keadaan semula
            button.prop('disabled', false).html('<i class="fas fa-magic"></i> Bantu Tulis');
        });
    });
});
</script>