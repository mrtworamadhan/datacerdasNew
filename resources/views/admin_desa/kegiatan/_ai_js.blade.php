<script>
document.querySelectorAll('.ai-helper-btn').forEach(button => {
    button.addEventListener('click', function() {
        const targetSelector = this.dataset.target;
        const section = this.dataset.section;
        const targetTextarea = document.querySelector(targetSelector);
        const context = document.getElementById('nama_kegiatan').value;
        const namaLembaga = document.getElementById('nama_lembaga').value;
        const namaDesa = document.getElementById('nama_desa').value;
        const lokasiKegiatan = document.getElementById('lokasi_kegiatan').value;
        const anggaran = document.getElementById('anggaran').value;
        const tanggal = document.getElementById('tanggal').value;

        if (!context) {
            alert('Silakan isi "Nama Kegiatan" terlebih dahulu sebagai konteks.');
            return;
        }

        // Tampilkan loading
        this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
        this.disabled = true;

        // Kirim request ke AI
        fetch('{{ route("ai.generate.text") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                context: context,
                section: section,
                nama_lembaga: namaLembaga,
                nama_desa: namaDesa,
                lokasi_kegiatan: lokasiKegiatan,
                anggaran: anggaran,
                tanggal: tanggal
            })
        })
        .then(response => {
            // PERBAIKAN: Kita akan selalu mencoba membaca respons, apa pun statusnya
            const contentType = response.headers.get("content-type");
            if (contentType && contentType.indexOf("application/json") !== -1) {
                // Jika server merespons dengan JSON (baik sukses maupun error)
                return response.json().then(data => ({ ok: response.ok, status: response.status, data }));
            } else {
                // Jika server merespons dengan HTML (kemungkinan besar halaman error)
                return response.text().then(text => {
                    // Log HTML mentah ke console untuk debugging
                    console.error("Server Response (Non-JSON):", text);
                    // Buat objek error standar
                    return Promise.reject({ ok: false, status: response.status, message: 'Server mengembalikan respons yang tidak valid. Cek console untuk detail.' });
                });
            }
        })
        .then(result => {
            if (result.ok) {
                // Jika sukses (status 2xx)
                targetTextarea.value = result.data.text;
            } else {
                // Jika gagal tapi responsnya JSON (misal: error validasi 422)
                throw new Error(result.data.message || `Terjadi error di server (Status: ${result.status})`);
            }
        })
        .catch(error => {
            console.error('Final Error:', error);
            alert('Terjadi kesalahan: ' + (error.message || 'Gagal terhubung.'));
        })
        .finally(() => {
            // Kembalikan tombol ke keadaan semula
            this.innerHTML = '<i class="fas fa-magic"></i> Bantu Tulis';
            this.disabled = false;
        });
    });
});
</script>