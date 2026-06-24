# Panduan Workflow Kasir - Tinta Sanita

## 🎯 Peran Kasir

Kasir memiliki tanggung jawab untuk mengelola:
1. **Reservasi online** - pelanggan yang booking melalui website
2. **Reservasi walk-in booking** - pengunjung yang datang dan ingin booking untuk tanggal lain
3. **Tiket langsung** - pengunjung yang datang dan langsung masuk hari itu (NEW!)

---

## 📋 Skenario 1: Reservasi Online + Walk-in Customer

### Situasi:
- Pelanggan sudah melakukan pemesanan melalui website
- Pelanggan datang ke lokasi wisata
- Kasir perlu menerima pembayaran

### Langkah-langkah:

1. **Buka Menu "Kelola Reservasi"**
   - Dari sidebar: Reservasi → Kelola Reservasi
   - Atau dari dashboard: Klik "Kelola Reservasi"

2. **Cari Reservasi Pelanggan**
   - Gunakan filter atau pencarian berdasarkan nama/WhatsApp
   - Lihat status pembayaran (Belum Bayar / Terbayar)

3. **Proses Pembayaran**
   - Klik tombol "Bayar Sekarang" pada reservasi yang belum terbayar
   - Modal akan menampilkan:
     - Nama pelanggan
     - Jumlah total yang harus dibayar
     - Detail paket
   - Jika sudah bayar, status berubah menjadi "Terbayar"

4. **Cetak Tiket**
   - Setelah pembayaran berhasil, klik tombol "Lihat Detail"
   - Di halaman detail, ada tombol "Cetak Tiket"
   - Berikan tiket kepada pelanggan

---

## 👥 Skenario 2: Walk-in Customer (Booking On-the-spot)

### Situasi:
- Pengunjung datang langsung tanpa pemesanan sebelumnya
- Ingin booking untuk tanggal lain dan membayar di tempat

### Langkah-langkah:

1. **Buat Reservasi Baru**
   - Dari sidebar: Reservasi → (tidak ada menu khusus, gunakan dashboard)
   - Dari dashboard: Klik "Reservasi Baru"
   - Atau dari "Kelola Reservasi": Klik "Reservasi Baru"

2. **Isi Form Reservasi**
   - **Pilih/Tambah Pengguna**
     - Jika pelanggan sudah terdaftar: Pilih dari dropdown
     - Jika belum terdaftar: Admin perlu menambahkan pengguna terlebih dahulu
   - **Pilih Paket**: Sesuai pilihan pelanggan
   - **Tanggal Kunjungan**: Hari ini atau tanggal lain
   - **Jumlah Pengunjung**: Jumlah orang yang akan berkunjung
   - **Catatan**: Opsional, untuk kebutuhan khusus
   - Harga akan otomatis dihitung (Weekday/Weekend)

3. **Buat Reservasi**
   - Klik tombol "Buat Reservasi"
   - Sistem akan membuat reservasi dengan status "Pending"
   - Catat ID reservasi yang ditampilkan

4. **Terima Pembayaran**
   - Dari sidebar: Reservasi → Terima Pembayaran
   - Atau dari dashboard: Klik "Terima Pembayaran"
   - Cari reservasi yang baru dibuat (berdasarkan ID atau nama pelanggan)
   - Klik tombol "Terima Bayar"
   - Isi metode pembayaran (Cash, Transfer, Kartu, dll)
   - Konfirmasi pembayaran
   - Status reservasi berubah menjadi "Confirmed"

5. **Cetak Tiket**
   - Kembali ke "Kelola Reservasi"
   - Cari reservasi yang sudah dibayar
   - Klik "Lihat Detail"
   - Klik "Cetak Tiket"
   - Berikan tiket kepada pelanggan

---

## 🎫 Skenario 3: Penjualan Tiket Langsung (NEW!)

### Situasi:
- Pengunjung datang langsung tanpa pemesanan
- Ingin langsung masuk ke wisata **HARI INI** (bukan booking)
- Langsung bayar dan mendapat tiket

### Langkah-langkah:

1. **Buka Menu "Penjualan Tiket Langsung"**
   - Dari sidebar: Reservasi → Penjualan Tiket Langsung
   - Atau dari dashboard: Klik "Penjualan Tiket"

2. **Isi Data Pelanggan**
   - **Nama Lengkap**: Nama pengunjung
   - **Nomor WhatsApp**: Untuk kontak dan komunikasi
   - (Jika pelanggan baru, sistem otomatis mendaftarkan)

3. **Pilih Paket & Jumlah Tiket**
   - **Paket Wisata**: Pilih paket yang diinginkan
   - **Jumlah Tiket**: Bisa beli multiple tiket sekaligus
   - Harga otomatis dihitung sesuai hari ini (Weekday/Weekend)

4. **Pilih Metode Pembayaran**
   - Cash
   - Transfer Bank
   - Kartu Kredit/Debit
   - Dll sesuai setting

5. **Proses & Cetak Tiket**
   - Klik "Proses & Cetak Tiket"
   - Sistem otomatis:
     - Membuat reservasi dengan tanggal hari ini
     - Mencatat pembayaran sebagai selesai
     - Menampilkan halaman cetak tiket
   - Cetak tiket dan berikan ke pengunjung

### Perbedaan Tiket Langsung vs Booking:
| Aspek | Tiket Langsung | Booking |
|-------|---|---|
| Berlaku | Hari ini saja | Tanggal yang dipilih |
| Pembayaran | Langsung saat beli | Saat check-in / konfirmasi |
| Prosesnya | Cepat (1 halaman) | Lebih detail |
| Identitas | Hanya nama & HP | Bisa lengkap |
| Fleksibilitas | Tidak bisa diubah | Bisa edit tanggal |

---

## 🔄 Menu Utama Kasir

### Dashboard
- Ringkasan statistik dan quick actions
- 4 tombol utama:
  - **Kelola Reservasi**: Melihat semua reservasi dan status pembayaran
  - **Reservasi Baru**: Membuat reservasi baru (walk-in booking)
  - **Penjualan Tiket**: Menjual tiket untuk hari ini (NEW!)
  - **Terima Pembayaran**: Memproses pembayaran reservasi

### Kelola Reservasi
- Lihat semua reservasi yang pending/confirmed
- Filter berdasarkan status
- Cari berdasarkan nama/WhatsApp pelanggan
- Edit data reservasi (tanggal, jumlah orang, status)
- Lihat detail dan cetak tiket
- Kirim tiket via WhatsApp

### Penjualan Tiket Langsung (NEW!)
- Form cepat untuk penjualan tiket hari ini
- Input data pelanggan
- Pilih paket dan jumlah
- Proses pembayaran langsung
- Cetak tiket seketika

### Terima Pembayaran
- Lihat semua reservasi yang belum terbayar
- Cari pelanggan
- Proses pembayaran dengan metode pilihan
- Automatic status update menjadi "Confirmed" setelah pembayaran

### Paket (View Only)
- Melihat daftar paket wisata yang tersedia
- Melihat harga weekday dan weekend
- Referensi untuk menjelaskan paket kepada pelanggan

---

## 💡 Tips & Trik

### Untuk Pengunjung Online:
1. Tunggu pelanggan datang
2. Langsung ke "Kelola Reservasi" → Cari pelanggan
3. Terima pembayaran
4. Cetak tiket

### Untuk Walk-in Booking:
1. Buat reservasi baru
2. Langsung terima pembayaran
3. Cetak tiket
4. Selesai!

### Untuk Tiket Langsung (Hari Ini):
1. Klik "Penjualan Tiket Langsung"
2. Isi data pelanggan
3. Pilih paket & jumlah
4. Bayar & cetak tiket
5. Selesai dalam 1 menit!

### Status Reservasi:
- **Pending**: Reservasi dibuat tapi belum ada pembayaran
- **Confirmed**: Sudah ada pembayaran, siap untuk kunjungan
- **Completed**: Pelanggan sudah selesai berkunjung

### Status Pembayaran:
- **Belum Bayar**: Belum ada pembayaran dari pelanggan
- **Terbayar**: Pembayaran sudah diterima

---

## ⚠️ Penting

1. **Verifikasi Data Pelanggan**: Pastikan nama dan nomor WhatsApp benar sebelum buat reservasi
2. **Harga Otomatis**: Harga dihitung berdasarkan hari kunjungan (Weekday/Weekend)
3. **Status Update**: Setelah pembayaran, status harus berubah menjadi "Confirmed"
4. **Tiket Langsung**: HANYA berlaku untuk hari berjalan, tidak bisa diubah tanggalnya
5. **Tiket**: Selalu cetak/kirim tiket setelah pembayaran berhasil
6. **Catatan**: Gunakan field catatan untuk kebutuhan khusus pelanggan (group, acara khusus, dll)

---

**Versi**: 2.0  
**Terakhir Update**: Juni 2026  
**Fitur Baru**: Penjualan Tiket Langsung
