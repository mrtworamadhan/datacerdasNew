<script>
$(document).ready(function() {
    // Fungsi umum untuk memanggil AI
    function callAiHelper(button, data) {
        button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
        
        fetch('{{ route("ai.generate.text") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(response => response.ok ? response.json() : response.json().then(err => Promise.reject(err)))
        .then(result => {
            const targetTextarea = $(button.data('target'));
            targetTextarea.val(result.text); // Ganti isi, bukan menambah
        })
        .catch(error => alert('Terjadi kesalahan: ' + (error.message || 'Gagal terhubung.')))
        .finally(() => button.prop('disabled', false).html('<i class="fas fa-magic"></i> ' + button.text().trim()));
    }

    // Event listener untuk tombol BANTU TULIS (buat dari nol)
    $('.ai-helper-btn').on('click', function() {
        const dataToSend = {
            _token: "{{ csrf_token() }}",
            context: $('#nama_kegiatan').val(),
            section: $(this).data('section'),
            penyelenggara_nama: $('#penyelenggara_nama').val(),
            nama_desa: $('#nama_desa').val(),
            lokasi_kegiatan: $('#lokasi_kegiatan').val(),
        };
        callAiHelper($(this), dataToSend);
    });

    // Event listener untuk tombol REWRITE
    $('.ai-rewrite-btn').on('click', function() {
        const dataToSend = {
            _token: "{{ csrf_token() }}",
            context: $('#nama_kegiatan').val(),
            section: $(this).data('section'),
            original_text: $(this).data('source-text'), // Kirim teks asli
            penyelenggara_nama: $('#penyelenggara_nama').val(),
            nama_desa: $('#nama_desa').val(),
            lokasi_kegiatan: $('#lokasi_kegiatan').val(),
        };
        callAiHelper($(this), dataToSend);
    });
});
</script>