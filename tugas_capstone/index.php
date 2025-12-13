<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Wisata Situ Cipanten</title>
    <script src="https://cdn.tailwindcss.com"></script>
  </head>
  <body class="bg-gray-50">
    <?php include 'includes/header.php'; ?>

    <!-- Home -->
    <section
      id="hero"
      class="relative h-screen flex flex-col justify-center items-center text-center text-white scroll-mt-[90px]"
      style="
        background-image: url('assets/images/Cipanten.webp');
        background-size: cover;
        background-position: center;
      "
    >
      <div class="absolute inset-0 bg-black/40"></div>

      <div class="relative z-20 p-4 pt-[90px] md:pt-0">
        <h1
          class="text-5xl md:text-7xl font-extrabold mb-4 drop-shadow-lg tracking-wide"
        >
          Jelajahi Keindahan Situ Cipanten
        </h1>
        <p class="text-xl md:text-3xl font-light drop-shadow-md">
          Danau Biru di Jantung Majalengka
        </p>
        <a
          href="#paket"
          class="mt-8 inline-block bg-teal-600 hover:bg-teal-700 text-white text-lg font-semibold py-3 px-8 rounded-full transition duration-300 shadow-xl transform hover:scale-105"
        >
          Lihat Paket Wisata
        </a>
      </div>
    </section>

    <!-- About -->
    <section
      id="about"
      class="container mx-auto py-12 px-4 scroll-mt-[50px] pt-[50px]"
    >
      <h2 class="text-4xl font-bold text-center text-teal-700 mb-6">
        Tentang Situ Cipanten
      </h2>

      <div class="flex flex-col md:flex-row gap-10">
        <div class="md:w-1/2">
          <p class="text-lg text-gray-700 mb-4">
            Situ Cipanten adalah destinasi wisata alam di Majalengka, Jawa
            Barat, yang terkenal karena keindahan danau alami dengan air
            jernihnya dan fenomena perubahan warna airnya menjadi biru saat
            musim hujan dan hijau/cokelat saat kemarau. Danau ini didukung oleh
            tujuh mata air abadi dan menjadi habitat bagi banyak ikan, serta
            menawarkan berbagai aktivitas menarik seperti naik perahu,
            berswafoto, dan berenang di area yang aman. Situ Cipanten juga
            memiliki fasilitas seperti penginapan dan area perkemahan. Situ
            Cipanten menawarkan pesona danau yang tenang dan dikelilingi oleh
            pepohonan rindang. Tempat sempurna untuk melepaskan penat, menikmati
            alam Majalengka, dan berfoto di spot-spot ikonik seperti jembatan
            kayu dan rumah pohon.
          </p>

          <div
            class="bg-teal-100 p-4 rounded-lg border-l-4 border-teal-500 mt-6"
          >
            <h3 class="text-xl font-semibold text-teal-700 mb-2">
              Informasi Tiket Masuk
            </h3>
            <p class="text-gray-800 text-justify">
              Tiket masuk Situ Cipanten adalah Rp10.000 untuk dewasa dan Rp5.000
              untuk anak-anak.
            </p>
            <p class="text-sm text-gray-600 mt-1">
              Harga dapat berubah sewaktu-waktu.
            </p>
          </div>
        </div>

        <div class="md:w-1/2">
          <h3 class="text-2xl font-bold text-teal-700 mb-4">
            Video Eksplorasi Situ Cipanten
          </h3>
          <div
            class="relative w-full aspect-video rounded-lg shadow-xl overflow-hidden"
          >
            <iframe
              class="absolute top-0 left-0 w-full h-full"
              src="https://www.youtube.com/embed/eBqE3TiA8ZQ"
              title="Video Promosi Situ Cipanten"
              frameborder="0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
              allowfullscreen
            >
            </iframe>
          </div>
        </div>
      </div>
    </section>

    <!-- fasilitas wisata -->
    <section
      id="fasilitas"
      class="container mx-auto py-12 px-4 scroll-mt-[50px] pt-[50px]"
    >
      <h2 class="text-4xl font-bold text-center text-emerald-700 mb-10">
        Fasilitas Wisata Situ Cipanten
      </h2>
      <p class="text-lg text-gray-600 text-center max-w-3xl mx-auto mb-10">
        Situ Cipanten menyediakan berbagai fasilitas permainan air dan darat
        yang seru untuk melengkapi liburan Anda, cocok untuk keluarga dan teman.
      </p>

      <div class="grid gap-8 grid-cols-2 sm:grid-cols-3 lg:grid-cols-5">
        <div
          class="facility-card bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition duration-300 transform hover:scale-105"
        >
          <img
            src="assets/images/kapal.webp"
            alt="Foto Kapal Dayung"
            class="w-40 h-40 object-cover rounded-lg mx-auto mb-3"
          />
          <h3 class="text-xl font-semibold mb-1 text-gray-800">Kapal Dayung</h3>
          <p class="text-sm text-gray-500">Menyusuri danau</p>
          <p class="text-lg font-bold text-orange-500 mt-2">
            Rp 10.000 / orang
          </p>
        </div>

        <div
          class="facility-card bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition duration-300 transform hover:scale-105"
        >
          <img
            src="assets/images/Cipanten.webp"
            alt="Foto Aynan Tepi Danau"
            class="w-40 h-40 object-cover rounded-lg mx-auto mb-3"
          />
          <h3 class="text-xl font-semibold mb-1 text-gray-800">Ayunan</h3>
          <p class="text-sm text-gray-500">Ayunan tepi danau</p>
          <p class="text-lg font-bold text-orange-500 mt-2">Rp 5.000 / orang</p>
        </div>

        <div
          class="facility-card bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition duration-300 transform hover:scale-105"
        >
          <img
            src="assets/images/bebek3.jpg"
            alt="Foto Bebek Goes"
            class="w-40 h-40 object-cover rounded-lg mx-auto mb-3"
          />
          <h3 class="text-xl font-semibold mb-1 text-gray-800">Bebek Goes</h3>
          <p class="text-sm text-gray-500">Sepeda air bentuk bebek</p>
          <p class="text-lg font-bold text-orange-500 mt-2">
            Rp 20.000 / orang
          </p>
        </div>

        <div
          class="facility-card bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition duration-300 transform hover:scale-105"
        >
          <img
            src="assets/images/sepeda1.webp"
            alt="Foto Sepeda Gantung"
            class="w-40 h-40 object-cover rounded-lg mx-auto mb-3"
          />
          <h3 class="text-xl font-semibold mb-1 text-gray-800">
            Sepeda Gantung
          </h3>
          <p class="text-sm text-gray-500">Melintasi danau dari atas</p>
          <p class="text-lg font-bold text-orange-500 mt-2">
            Rp 25.000 / orang
          </p>
        </div>

        <div
          class="facility-card bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition duration-300 transform hover:scale-105"
        >
          <img
            src="assets/images/pelampung.jpg"
            alt="Foto Pelampung air"
            class="w-40 h-40 object-cover rounded-lg mx-auto mb-3"
          />
          <h3 class="text-xl font-semibold mb-1 text-gray-800">Pelampung</h3>
          <p class="text-sm text-gray-500">Bermain air dengan aman</p>
          <p class="text-lg font-bold text-orange-500 mt-2">
            Rp 10.000 / orang
          </p>
        </div>

        <div
          class="facility-card bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition duration-300 transform hover:scale-105"
        >
          <img
            src="assets/images/motor.jpeg"
            alt="Foto Pelampung air"
            class="w-40 h-40 object-cover rounded-lg mx-auto mb-3"
          />
          <h3 class="text-xl font-semibold mb-1 text-gray-800">
            Parkiran Motor
          </h3>
          <p class="text-sm text-gray-500">Tempat parkiran motor</p>
          <p class="text-lg font-bold text-orange-500 mt-2">Rp 5.000 / orang</p>
        </div>

        <div
          class="facility-card bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition duration-300 transform hover:scale-105"
        >
          <img
            src="assets/images/mobil.jpg"
            alt="Foto Pelampung air"
            class="w-40 h-40 object-cover rounded-lg mx-auto mb-3"
          />
          <h3 class="text-xl font-semibold mb-1 text-gray-800">
            Parkiran Mobil
          </h3>
          <p class="text-sm text-gray-500">Tempat parkiran mobil</p>
          <p class="text-lg font-bold text-orange-500 mt-2">
            Rp 10.000 / orang
          </p>
        </div>
      </div>
    </section>

    <!-- Paket Wisata -->
    <section
      id="paket"
      class="bg-gray-50 py-16 px-4 scroll-mt-[50px] pt-[50px]"
    >
      <h2 class="text-4xl font-bold text-center text-teal-700 mb-10">
        Daftar Paket Wisata Terbaik
      </h2>

      <div
        class="container mx-auto grid gap-8 grid-cols-1 md:grid-cols-2 lg:grid-cols-3"
      >
        <article
          class="bg-white rounded-xl shadow-xl overflow-hidden hover:shadow-2xl transition duration-300"
        >
          <img
            src="assets/images/bebek4.jpeg"
            alt="Foto Paket Wisata Air"
            class="w-full h-48 object-cover"
          />
          <div class="p-6">
            <h3 class="text-2xl font-semibold text-gray-800 mb-2">
              <b>Paket Petualangan Air Cipanten</b>
            </h3>
            <p class="text-sm text-gray-500 mb-4">
              Bundel Kapal Dayung, Bebek Goes (30 menit), dan Pelampung. (Lebih
              Hemat!)
            </p>
            <div class="flex justify-between items-center">
              <p class="text-lg font-bold text-orange-600">
                Hanya <b>Rp 45.000</b>/pax
              </p>
              <button
                class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition"
              >
                Pesan
              </button>
            </div>
          </div>
        </article>

        <article
          class="bg-white rounded-xl shadow-xl overflow-hidden hover:shadow-2xl transition duration-300"
        >
          <img
            src="assets/images/paket2.jpg"
            alt="Foto Sepeda Gantung"
            class="w-full h-48 object-cover"
          />
          <div class="p-6">
            <h3 class="text-2xl font-semibold text-gray-800 mb-2">
              <b>Paket Sepeda Gantung & Danau</b>
            </h3>
            <p class="text-sm text-gray-500 mb-4">
              Tiket Masuk 2 orang, Sepeda Gantung (1x), dan Kapal Dayung (30
              menit).
            </p>
            <div class="flex justify-between items-center">
              <p class="text-lg font-bold text-orange-600">
                Hanya <b>Rp 75.000</b>/2 pax
              </p>
              <button
                class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition"
              >
                Pesan
              </button>
            </div>
          </div>
        </article>

        <article
          class="bg-white rounded-xl shadow-xl overflow-hidden hover:shadow-2xl transition duration-300"
        >
          <img
            src="assets/images/paket3.jpg"
            alt="Foto Museum Salak"
            class="w-full h-48 object-cover"
          />
          <div class="p-6">
            <h3 class="text-2xl font-semibold text-gray-800 mb-2">
              <b>Paket Edukasi & Foto Spot</b>
            </h3>
            <p class="text-sm text-gray-500 mb-4">
              Tiket Masuk, Akses Museum Salak, dan Ayunan Tepi Danau Bebas.
            </p>
            <div class="flex justify-between items-center">
              <p class="text-lg font-bold text-orange-600">
                Mulai <b>Rp 30.000</b>/pax
              </p>
              <button
                class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition"
              >
                Pesan
              </button>
            </div>
          </div>
        </article>
      </div>
    </section>

    <!-- Galery -->
    <section
      id="galery"
      class="container mx-auto py-16 px-4 scroll-mt-[50px] pt-[50px]"
    >
      <h2 class="text-4xl font-bold text-center text-teal-700 mb-10">
        Galeri Foto Keindahan Situ Cipanten
      </h2>

      <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <div class="overflow-hidden rounded-lg shadow-xl group cursor-pointer">
          <img
            src="assets/images/danau.jpg"
            alt="Pemandangan Danau"
            class="w-full h-64 object-cover transition duration-500 group-hover:scale-110"
          />
          <div class="p-3 text-sm text-center bg-white">
            Pemandangan Danau Tenang
          </div>
        </div>

        <div class="overflow-hidden rounded-lg shadow-xl group cursor-pointer">
          <img
            src="assets/images/danau1.jpeg"
            alt="Pemandangan Danau"
            class="w-full h-64 object-cover transition duration-500 group-hover:scale-110"
          />
          <div class="p-3 text-sm text-center bg-white">
            Aktivitas Bermain dengan Ikan
          </div>
        </div>

        <div class="overflow-hidden rounded-lg shadow-xl group cursor-pointer">
          <img
            src="assets/images/bebek1.jpeg"
            alt="Aktivitas Bebek Goes"
            class="w-full h-64 object-cover transition duration-500 group-hover:scale-110"
          />
          <div class="p-3 text-sm text-center bg-white">
            Aktivitas Bebek Goes
          </div>
        </div>

        <div class="overflow-hidden rounded-lg shadow-xl group cursor-pointer">
          <img
            src="assets/images/sepeda2.jpg"
            alt="Sepeda Gantung"
            class="w-full h-64 object-cover transition duration-500 group-hover:scale-110"
          />
          <div class="p-3 text-sm text-center bg-white">
            Spot Sepeda Gantung
          </div>
        </div>

        <div class="overflow-hidden rounded-lg shadow-xl group cursor-pointer">
          <img
            src="assets/images/bebek4.jpeg"
            alt="Jembatan Kayu"
            class="w-full h-64 object-cover transition duration-500 group-hover:scale-110"
          />
          <div class="p-3 text-sm text-center bg-white">
            Jembatan Kayu Ikonik
          </div>
        </div>

        <div class="overflow-hidden rounded-lg shadow-xl group cursor-pointer">
          <img
            src="assets/images/ayunan.webp"
            alt="Ayunan Tepi Danau"
            class="w-full h-64 object-cover transition duration-500 group-hover:scale-110"
          />
          <div class="p-3 text-sm text-center bg-white">
            Spot Ayunan Tepi Danau
          </div>
        </div>

        <div class="overflow-hidden rounded-lg shadow-xl group cursor-pointer">
          <img
            src="assets/images/pelampung1.jpg"
            alt="Aktivitas Pelampung Air"
            class="w-full h-64 object-cover transition duration-500 group-hover:scale-110"
          />
          <div class="p-3 text-sm text-center bg-white">
            Aktivitas Pelampung Air
          </div>
        </div>

        <div class="overflow-hidden rounded-lg shadow-xl group cursor-pointer">
          <img
            src="assets/images/kapal1.jpg"
            alt="Kapal Terapung"
            class="w-full h-64 object-cover transition duration-500 group-hover:scale-110"
          />
          <div class="p-3 text-sm text-center bg-white">
            Spot Kapal Terapung
          </div>
        </div>

        <div class="overflow-hidden rounded-lg shadow-xl group cursor-pointer">
          <img
            src="assets/images/sepeda.jpg"
            alt="Sepeda Gantung"
            class="w-full h-64 object-cover transition duration-500 group-hover:scale-110"
          />
          <div class="p-3 text-sm text-center bg-white">
            Spot Sepeda Gantung
          </div>
        </div>
      </div>
    </section>

    <main class="flex-grow"></main>

    <?php include 'includes/footer.php'; ?>

    <script>
      const menuButton = document.getElementById("menu-button");
      const mobileMenu = document.getElementById("mobile-menu");
      const menuLinks = mobileMenu.querySelectorAll("a");

      // Fungsi untuk membuka/menutup menu
      function toggleMenu() {
        mobileMenu.classList.toggle("hidden");
      }

      // 1. Tambahkan event listener untuk tombol hamburger
      menuButton.addEventListener("click", toggleMenu);

      // 2. Tambahkan event listener pada setiap link di menu mobile
      // Tujuannya agar menu tertutup otomatis setelah link diklik
      menuLinks.forEach((link) => {
        link.addEventListener("click", () => {
          if (!mobileMenu.classList.contains("hidden")) {
            toggleMenu(); // Tutup menu setelah klik
          }
        });
      });
    </script>
  </body>
</html>
