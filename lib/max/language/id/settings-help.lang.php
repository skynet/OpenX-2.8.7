<?php

/*
+---------------------------------------------------------------------------+
| OpenX v2.8                                                                |
| ==========                                                                |
|                                                                           |
| Copyright (c) 2003-2009 OpenX Limited                                     |
| For contact details, see: http://www.openx.org/                           |
|                                                                          |
| This program is free software; you can redistribute it and/or modify      |
| it under the terms of the GNU General Public License as published by      |
| the Free Software Foundation; either version 2 of the License, or         |
| (at your option) any later version.                                       |
|                                                                           |
| This program is distributed in the hope that it will be useful,           |
| but WITHOUT ANY WARRANTY; without even the implied warranty of            |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
| GNU General Public License for more details.                              |
|                                                                           |
| You should have received a copy of the GNU General Public License         |
| along with this program; if not, write to the Free Software               |
| Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA |
+---------------------------------------------------------------------------+
$Id: settings-help.lang.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

// Settings help translation strings
$GLOBALS['phpAds_hlp_dbhost'] = "\n        Menetapkan nama Host untuk ".$phpAds_dbmsname." database server yang ingin digunakan untuk koneksi.\n		";

$GLOBALS['phpAds_hlp_dbport'] = "\n        Menetapkan nomor dari port database server yang ingin digunakan untuk koneksi ke ".$phpAds_dbmsname.".\n		Nomor port Default untuk database ".$phpAds_dbmsname." adalah <i>". (MAX_PRODUCT_NAME == 'phpAdsNew' ? '3306' : '5432')."</i>.\n		";

$GLOBALS['phpAds_hlp_dbuser'] = "\n        Menetapkan nama user yang digunakan oleh ".MAX_PRODUCT_NAME." untuk mengakses ".$phpAds_dbmsname." database server.\n		";

$GLOBALS['phpAds_hlp_dbpassword'] = "\n        Menetapkan kata sandi yang digunakan oleh ".MAX_PRODUCT_NAME." untuk mengakses database server ".$phpAds_dbmsname.".\n		";

$GLOBALS['phpAds_hlp_dbname'] = "\n        Menetapkan nama database yang digunakan oleh ".MAX_PRODUCT_NAME." untuk menyimpan data.\n		Penting: Database harus sudah dibuat pada database server. ".MAX_PRODUCT_NAME." <b>tidak</b>\n		membuat database dengan sendirinya bila database tersebut belu tersedia.\n		";

$GLOBALS['phpAds_hlp_persistent_connections'] = "\n	Penggunaan koneksi persistent sangat mempercepatkan ".MAX_PRODUCT_NAME." dan\n		meringankan beban pada server. Tetapi hal ini ada kekurangan, yaitu bilamana situs Anda\n		dikunjungi oleh banyak pengunjung justru beban pada server bisa meningkat lebih drastis\n		dibandingkan dengan pengunaan koneksi biasa. Pertimbangan apakah sebaiknya Anda mengunakan\n		koneksi biasa atau koneksi persistant tergantung pada jumlah pengunjung dan pada Hardware\n		yang dipakai. Bila ".MAX_PRODUCT_NAME." mengunakan tenaga server yang berkelebihan,\n		disarankan untuk periksa penyetelan ini paling pertama.\n		";

$GLOBALS['phpAds_hlp_insert_delayed'] = "\n	".$phpAds_dbmsname." mengunci tabel sewaktu mengisi data. Bila situs Anda banyak dikunjungi bisa terjadi\n		bahwa ".MAX_PRODUCT_NAME." harus menunggu untuk menambah baris baru sehubungan database\n		masih terkunci. Bila Anda menggunakan Insert Delayed, Anda tidak perlu lagi menunggu sehubungan\n		baris yang bersangkutan akan disisipkan sewaktu tabel tidak dipakai oleh proses yang lain.\n		";

$GLOBALS['phpAds_hlp_compatibility_mode'] = "\n	Bila Anda mengalami masalah dalam integrasi ".MAX_PRODUCT_NAME." dengan sebuah produk lain\n		cobalah aktivasikan Database Compatibility Mode. Bila Anda mengunakan invokasi Local Mode\n		sedangkan posisi dari Compatibility Mode berada dalam posisi On, ".MAX_PRODUCT_NAME."\n		akan membiarkan status pada koneksi ke database dalam keadaan seperti sebelum\n		".MAX_PRODUCT_NAME." dijalankan. Stelan ini agak lebih lambat (hanya sedikit) dan soal\n		itu dalam posisi Off secara Default.\n		";

$GLOBALS['phpAds_hlp_table_prefix'] = "\n	Bilamana database yang digunakan oleh ".MAX_PRODUCT_NAME." dibagi bersama-sama produk software yang\n		lain, lebih arif kalau Anda menggunakan sebuah <i>Prefix</i> untuk nama tabel. Bila Anda menggunakan lebih dari\n		satu instalasi dari ".MAX_PRODUCT_NAME." dalam database yang sama perlu dipastikan, bahwa Prefix\n		yang digunakan untuk setiap instalasi adalah unik.\n		";

$GLOBALS['phpAds_hlp_table_type'] = "\n        ".$phpAds_dbmsname." mendukung berberapa jenis tabel. Setiap jenis tabel memiliki khas tersendiri dan diantaranya\n		ada berberapa jenis yang mampu untuk amat mempercepat ".MAX_PRODUCT_NAME." Jenis tabel MyISAM adalah jenis\n		Default dan tersedia pada semua instalasi dari ".$phpAds_dbmsname.". Ada kemungkinan bahwa jenis tabel yang\n		lain tidak tersedia pada server Anda\n		";

$GLOBALS['phpAds_hlp_url_prefix'] = "\n        Untuk befungsi dengan baik ".MAX_PRODUCT_NAME." harus mengenal lokasi dirinya pada web server.\n		Anda perlu menetapkan URL ke direktori penyimpanan ".MAX_PRODUCT_NAME.", seb. contoh:\n		http://www.url-anda.com/".MAX_PRODUCT_NAME.".\n		";

$GLOBALS['phpAds_hlp_my_header'] =
$GLOBALS['phpAds_hlp_my_footer'] = "\n	Disini Anda perlu menetapkan <i>Path</i> ke file Header (contoh: /home/login/www/header.htm)\n		untuk mengadakan sebuah header dan/atau footer pada setiap halaman di Interface Admin.\n		Diperbolehkan untuk menggunakan teks atau html dalam file tersebut (bila Anda ingin\n		menggunakan html dalam satu atau kedua filenya jangan menggunakan <i>Tags</i> seperti &lt;body>\n		atau &lt;html>).\n		";

$GLOBALS['phpAds_hlp_content_gzip_compression'] = "\n	Dengan membolehkan kompresi GZIP Anda akan dapat mengurangi data yang dikirim kepada browser\n		setiap kalinya Interface Administrator dibuka yang cukup besar. Untuk mengaktifkan\n		fasilitas ini minimal versi PHP 4.0.5 dengan ekstensi GZIP perlu terinstal pada\n		server Anda.\n		";

$GLOBALS['phpAds_hlp_language'] = "\n	Menentukan bahasa yang digunakan oleh ".MAX_PRODUCT_NAME." sebagai Default. Bahasa\n		yang dipilih disini menjadi bahasa yang Default untuk Interface Admin dan untuk\n		Interface Pemasang Iklan. Mohon perhatikan: Diperbolehkan untuk menentukan bahasa\n		yang berbeda untuk setiap Pemasang Iklan pada Interface Admin termasuk izin\n		kepada Pemasang Iklan untuk memilih bahasa sesuai selera sendiri.\n		";

$GLOBALS['phpAds_hlp_name'] = "\n	Menentukan nama yang digunakan untuk aplikasi ini. Kata-kata yang diisi disini akan\n		ditampilkan pada seluruh halaman di Interface Admin dan Interface Pemasang Iklan.\n		Bila kotak ini tidak diisi (default) maka sebuah lambang dari ".MAX_PRODUCT_NAME."\n		akan tertampil pada halaman-halaman tersebut.\n		";

$GLOBALS['phpAds_hlp_company_name'] = "\n	Nama ini akan digunakan dalam E-Mail yang dikirim oleh ".MAX_PRODUCT_NAME.".\n		";

$GLOBALS['phpAds_hlp_override_gd_imageformat'] = "\n	Pada umumnya ".MAX_PRODUCT_NAME." akan mendeteksi secara otomatis apakah GD Library\n		terintal pada server Anda dan format apa saja yang didukung oleh versi GD tersebut.\n		Tetapi tetap bisa terjadi bahwa deteksi tersebut kurang akurat atau salah sehubungan\n		beberapa versi PHP tidak mengizinkan deteksi format gambar apa saja yang didukung.\n		Bila ".MAX_PRODUCT_NAME." gagal mendeteksi format gambar yang akurat Anda bisa menentukan\n		format yang benar disini. Nilai yang diperbolehkan adalah: none, png, jpeg, gif.\n		";

$GLOBALS['phpAds_hlp_p3p_policies'] = "\n	Bila Anda ingin mengaktifkan P3P Privacy Policies dari ".MAX_PRODUCT_NAME.", Anda\n		perlu mengubah stelan ini ke posisi On.\n		";

$GLOBALS['phpAds_hlp_p3p_compact_policy'] = "\n	Policy Kompak yang dikirim bersamaan dengan Cookie. Stelan Default adalah:\n		'CUR ADM OUR NOR STA NID' yang mengizinkan Internet Explorer 6 untuk terima\n		Cookie yang digunakan oleh ".MAX_PRODUCT_NAME.". Anda diperbolehkan untuk\n		menentukan Privacy Statement lain sesuai dengan apa yang digunakan oleh webserver\n		Anda disini.\n		";

$GLOBALS['phpAds_hlp_p3p_policy_location'] = "\n	Bila Anda ingin menggunakan Private Policy yang penuh, Anda bisa menentukan lokasi dari\n		Policy tersebut disini.\n		";

$GLOBALS['phpAds_hlp_log_beacon'] = "\n	Yang dimaksud dengan Rambu Kecil adalah gambar kecil yang tidak kelihatan oleh pengunjung dan yang ditempatkan pada\n		halaman yang sekalian menampilkan banner. Bila fungsi ini diaktifkan ".MAX_PRODUCT_NAME."\n		akan menggunakan sebuah gambar kecil limunan untuk menghitung jumlah Impression yang dicapai\n		oleh banner secara lebih akurat. Bila fungsi ini dimatikan seluruh Impression akan\n		dihitung sewaktu pengantaran banner, tetapi perhitungan tersebut tidak terlalu akurat\n		sehubungan banner yang diantarkan tidak selalu tertampil di layar pengunjung.\n		";

$GLOBALS['phpAds_hlp_compact_stats'] = "\n	Secara tradisional ".MAX_PRODUCT_NAME." melakukan pencatatan yang sangat luas dan terperinci\n		tetapi fasilitas ini mengakibatkan beban yang sangat besar pada database server. Hal ini\n		bisa membawa masalah pada situs dengan jumlah pengunjung yang tinggi. Untuk mengatasi masalah\n		tersebut ".MAX_PRODUCT_NAME." mendukung jenis statistik yang baru, yaitu Statistik Kompak\n		yang tidak terlalu membebankan database server tetapi tidak terlalu terperinci dalam catatannya.\n		Statistik Kompak mengkumpulkan jumlah AdViews dan jumlah AdClicks untuk setiap jam saja. Bila\n		Anda inginkan statistik yang terperinci, fungsi Statistik Kompak perlu dimatikan.\n		";

$GLOBALS['phpAds_hlp_log_adviews'] = "\n	Biasanya seluruh AdViews dicatat oleh ".MAX_PRODUCT_NAME.". Bila Anda tidak ingin mengetahui\n		statistik tentang AdViews, fungsi ini perlu dimatikan.\n		";

$GLOBALS['phpAds_hlp_block_adviews'] = "\n	Setiap kalinya seseorang pengunjung menampilkan ulang sebuah halaman, ".MAX_PRODUCT_NAME." akan\n		mencatat satu AdView. Fungsi ini menjaminkan, bahwa hanya satu AdView akan tercatat untuk\n		setiap banner unik dalam jangka waktu detik yang ditentukan oleh Anda. Sebagai contoh:\n		Bila Anda menentukan jangka waktu 300 detik, ".MAX_PRODUCT_NAME." hanya akan mencatat\n		AdViews bilamana banner yang sama belum ditampilkan kepada pengunjung yang bersangkutan\n		selama 5 menit terakhir. Fungsi ini hanya bekerja dengan cukup baik bila\n		browser pengunjung menerima Cookies.\n		";

$GLOBALS['phpAds_hlp_log_adclicks'] = "\n	Biasanya seluruh AdClicks dicatat oleh ".MAX_PRODUCT_NAME.". Bila Anda tidak ingin mengetahui\n		statistik tentang AdClicks, fungsi ini perlu dimatikan.\n		";

$GLOBALS['phpAds_hlp_block_adclicks'] = "\n	Bila seorang pengunjung meng-klik berulang-ulang sebuah banner, ".MAX_PRODUCT_NAME." akan\n		mencatat satu AdClick setiap kalinya. Fungsi ini menjaminkan, bahwa hanya satu AdClick\n		akan tercatat untuk setiap banner unik dalam jangka waktu detik yang ditentukan oleh Anda.\n		Sebagai contoh: Bila Anda menentukan jangka waktu 300 detik, ".MAX_PRODUCT_NAME." hanya\n		akan mencatat AdClicks bilamana banner yang sama belum di-klik oleh pengunjung yang bersangkutan\n		selama 5 menit terakhir. Fungsi ini hanya bekerja dengan cukup baik bila browser dari\n		pengunjung terima Cookies.\n		";

$GLOBALS['phpAds_hlp_log_source'] = "\n	Bila Anda gunakan fungsi parameter sumber <i>source parameter</i> dalam kode invokasi, informasi tersebut\n		bisa disimpan dalam database untuk melihat performa dari parameter sumber pada data statistik.\n		Bila Anda tidak menggunakan parameter sumber atau tidak ingin menyimpan informasi tentang parameter\n		tersebut, silakan matikan pilihan ini.\n		";

$GLOBALS['phpAds_hlp_geotracking_stats'] = "\n	Bila Anda menggunakan database untuk <i>Geotargeting</i>, Anda diperbolehkan untuk menyimpan informasi\n		geografis dalam database. Jika fungsi ini diaktifkan Anda dapat mengikuti statistik tentang\n		lokasi asal dari pengunjung dan performa dari setiap banner pada negara-negara berbeda.\n		Pilihan ini hanya tersedia bilamana Anda menggunakan statistik <i>Verbose</i>.\n		";

$GLOBALS['phpAds_hlp_log_hostname'] = "\n	Bila Anda ingin menyimpan nama host atau nomor IP dari setiap pengunjung dalam statistik, silakan aktifkan\n		pilihan ini. Menyimpan informasi ini akan memperlihatkan kepada Anda, host yang mana yang terima\n		paling banyak banner. Pilihan ini hanya tersedia bilamana Anda menggunakan statistik <i>Verbose</i>.\n		";

$GLOBALS['phpAds_hlp_log_iponly'] = "\n	Menyimpan nomor IP dari pengunjung akan membutuhkan ruang yang cukup besar dalam database. Bila Anda aktifkan\n		pilihan ini, ".MAX_PRODUCT_NAME." akan menyimpan informasi tentang Host, akantetapi hanya\n		alamat IP yang tersimpan pada database guna menghemat ruang. Pilihan ini tidak tersedia bilamana nama\n		dari host tidak disediahkan oleh server yang bersangkutan atau oleh ".MAX_PRODUCT_NAME." sehubungan\n		alamat IP menang selalu disimpan.\n		";

$GLOBALS['phpAds_hlp_reverse_lookup'] = "\n	Nama dari host pada umumnya ditentukan oleh web server tetapi kadang-kadang fasilitas ini tidak diaktifkan.\n		Bila Anda ingin menggunakan nama host dari pengunjung dalam batas penyampaian dan/atau ingin\n		mempertahankan statistik tentang ini tetapi server tidak menyediakan informasi tersebut, pilihan\n		ini perlu dimatikan. Penentuan nama host dari pengunjung membutuhkan waktu yang cukup lama;\n		hal ini akan memperlambat penyampaian banner.\n		";

$GLOBALS['phpAds_hlp_proxy_lookup'] = "\n	Beberapa pengunjung menggunakan proxy server untuk mengakses Internet. Dalam hal ini\n		".MAX_PRODUCT_NAME." akan mencatat nomor IP atau nama Host dari proxy server\n		tetapi bukan dari pengunjung. Bila fungsi ini diaktifkan, ".MAX_PRODUCT_NAME."\n		akan mencoba untuk temukan alamat pengunjung dibelakang proxy server. Bila alamat\n		sebenarnya tidak bisa ditemukan, akan tercatat alamat dari proxy server. Fungsi ini\n		tidak aktif secara default, sehubungan ia memperlambat pencatatan.\n		";

$GLOBALS['phpAds_hlp_auto_clean_tables'] =
$GLOBALS['phpAds_hlp_auto_clean_tables_interval'] = "\n	Bila Anda ingin gunakan fasilitas ini, statistik yang diperolehkan akan dihapus secara otomatis\n		setelah periode yang ditentukan pada kotak ini tercapai. Sebagai contoh: Bila Anda tentukan\n		jangka waktu 5 minggu, statistik yang melebihi jangka waktu 5 minggu akan dihapus secara\n		otomatis.\n		";

$GLOBALS['phpAds_hlp_auto_clean_userlog'] =
$GLOBALS['phpAds_hlp_auto_clean_userlog_interval'] = "\n	Pilihan ini akan menghapus semua catatan dari <i>Userlog</i> yang masa waktunya melebihi jumlah\n		minggu yang telah ditentukan pada kotak	dibawah ini.\n		";

$GLOBALS['phpAds_hlp_geotracking_type'] = "\n	<i>Geotargeting</i> mengizinkan ".MAX_PRODUCT_NAME." untuk mengubah alamat IP dari pengunjung ke\n		informasi geografis. Bedasarkan informasi ini Anda bisa menentukan batas penyampaian atau\n		menyimpan informasi guna untuk melihat negara yang mana yang memperoleh <i>Impressions</i>\n		atau <i>Click-trus</i> paling banyak. Bila Anda aktifkan <i>Geotargeting</i> Anda perlu\n		pilih jenis database yang digunakan. ".MAX_PRODUCT_NAME." pada saat ini mendukung database\n		IP2Country\n		atau <a href='http://www.maxmind.com/?rId=phpadsnew2' target='_blank'>GeoIP</a>.\n		";

$GLOBALS['phpAds_hlp_geotracking_location'] = "\n	Kecuali kalau Anda menggunakan modul GeoIP Apache, Anda perlu beritahukan database <i>Geotargeting</i>\n		yang ingin digunakan kepada ".MAX_PRODUCT_NAME.". Disarankan untuk menyimpan database yang\n		digunakan diluar document root dari web server untuk menghindarkan database tersebut bisa di-\n		download oleh pengguna.\n		";

$GLOBALS['phpAds_hlp_geotracking_cookie'] = "\n	Ubah alamat IP ke informasi geografis akan membutuhkan waktu yang cukup lama. Untuk menghindarkan perubahan\n		ini dilakukan setiapkalinya sebuah banner disampaikan oleh ".MAX_PRODUCT_NAME.", hasil perubahan\n		bisa disimpan dalam sebuah <i>Cookie</i>. Bila Cookie tersebut ini tersedia, ".MAX_PRODUCT_NAME."\n		akan gunakan informasi ini dan tidak lagi perlu mengubah alamat IP.\n		";

$GLOBALS['phpAds_hlp_ignore_hosts'] = "\n	Bila Anda ingin menghindar perhitungan Clicks dan Views oleh komputer tertentu, komputer-\n		komputer tersebut bisa dicatat pada daftar ini. Bila fasilitas <i>Reverse Lookup</i> diaktifkan,\n		Anda diperbolehkan untuk mencatat nama domain dan nomor IP disini. Bila fasilitas\n		<i>Reverse Lookup</i> tidak aktif, hanya nomor IP diperbolehkan disini. Anda boleh gunakan\n		Wildcards (contoh '*.altavista.com' or '192.168.*').\n		";

$GLOBALS['phpAds_hlp_begin_of_week'] = "\n	Pada umumnya sebuah minggu dimulai dengan hari senin. Bila Anda ingin memulai minggu\n		pada hari minggu, silakan tentukannya disini.\n		";

$GLOBALS['phpAds_hlp_percentage_decimals'] = "\n	Menentukan jumlah angka desimal pada halaman statistik.\n		";

$GLOBALS['phpAds_hlp_warn_admin'] = "\n	".MAX_PRODUCT_NAME." dapat mengirim E-mail, bilamana sisa Clicks atau sisa Views di\n	sebuah kampanye tinggal sedikit. Fasilitas ini aktif sebagai default.\n		";

$GLOBALS['phpAds_hlp_warn_client'] = "\n	".MAX_PRODUCT_NAME." dapat mengirim E-mail kepada Pemasang Iklan, bilamana sisa Clicks\n	atau sisa Views tinggal sedikit. Fasilitas ini aktif sebagai default.\n		";

$GLOBALS['phpAds_hlp_qmail_patch'] = "\n	Beberapa versi dari program qmail mengandung sebuah bug yang mengakibatkan ".MAX_PRODUCT_NAME."\n		tampilkan Headers dalam badan dari E-Mail. Bila Anda aktifkan fungsi ini, ".MAX_PRODUCT_NAME."\n		akan mengirimkan E-Mail dalam format yang kompatibel.\n		";

$GLOBALS['phpAds_hlp_warn_limit'] = "\n	Batas yang memerintah ".MAX_PRODUCT_NAME." untuk mengirim E-mail Peringatan. Angka\n	default adalah 100.\n		";

$GLOBALS['phpAds_hlp_allow_invocation_plain'] =
$GLOBALS['phpAds_hlp_allow_invocation_js'] =
$GLOBALS['phpAds_hlp_allow_invocation_frame'] =
$GLOBALS['phpAds_hlp_allow_invocation_xmlrpc'] =
$GLOBALS['phpAds_hlp_allow_invocation_local'] =
$GLOBALS['phpAds_hlp_allow_invocation_interstitial'] =
$GLOBALS['phpAds_hlp_allow_invocation_popup'] = "\n	Dengan penyetelan ini Anda tentukan jenis invokasi yang diperbolehkan. Jenis invokasi\n		yang di-tidakaktifkan disini tidak akan tersedia dalam fungsi pembuatan kode invokasi\n		/ kode banner secara otomatis. Penting: Metode-metode invokasi tetap berfungsi bila\n		di-tidakaktifkan tetapi tidak lagi tersedia untuk di-generate.\n		";

$GLOBALS['phpAds_hlp_con_key'] = "\n	".MAX_PRODUCT_NAME." memiliki sistem pencarian yang sangat kuat dengan menggunakan\n		seleksi langsung. Untuk informasi lebih lanjut mohon konsultasikan buku pedoman. Dengan\n		opsi ini Anda menghidupkan fungsi Kata Kunci Konditional. Stelan ini berada dalam\n		posisi On secara Default.\n		";

$GLOBALS['phpAds_hlp_mult_key'] = "\n	Bila Anda gunakan seleksi langsung untuk menampilkan banner Anda diperbolehkan untuk\n		menggunakan satu atau lebih kata kunci untuk setiap banner. Opsi ini harus di\n		posisi On bila Anda ingin menentukan lebih dari satu kata kunci. Stelan ini berada\n		dalam posisi On secara Default.\n		";

$GLOBALS['phpAds_hlp_acl'] = "\n	Bila Anda tidak menggunakan pembatasan penyampaian silakan matikan opsi ini dengan parameter\n		ini. Stelan yang berada dalam posisi Off akan meningkatkan kecepatan dari\n		".MAX_PRODUCT_NAME.".\n        	";

$GLOBALS['phpAds_hlp_default_banner_url'] =
$GLOBALS['phpAds_hlp_default_banner_target'] = "\n	Bila ".MAX_PRODUCT_NAME." gagal menghubungi database server atau tidak bisa temukan banner\n		yang sesuai didasarkan database <i>crashed</i> atau terhapus, ia tidak akan menampilkan\n		apapun. Berberapa pengguna ingin menentukan banner default yang tertampil bila terjadinya\n		serupa. Banner default yang ditentukan disini tidak akan dicatat dan tidak ditampilkan\n		selama masih ada banner yang aktif dalam database. Stelan ini berada dalam posisi Off\n		secara Default.\n		";

$GLOBALS['phpAds_hlp_delivery_caching'] = "\n	Guna untuk mempercepat penyampaian, ".MAX_PRODUCT_NAME." menggunakan sebuah Cache yang berisi\n		seluruh informasi yang diperlukan untuk menyampaikan banner kepada pengunjung halam web\n		Anda. Cache penyampaian ini secara default akan disimpan pada database. Untuk meningkatkan\n		kecepatan diperbolehkan untuk menyimpan Cache tersebut dalam file atau dalam <i>shared\n		memory</i>. Shared memory adalah yang paling cepat, penyimpanan dalam file juga cukup\n		cepat. Tidak disarankan untuk mematikan Cache penyampaian sehubungan hal ini akan benar-\n		benar berpengaruh pada performa.\n		";

$GLOBALS['phpAds_hlp_type_sql_allow'] =
$GLOBALS['phpAds_hlp_type_web_allow'] =
$GLOBALS['phpAds_hlp_type_url_allow'] =
$GLOBALS['phpAds_hlp_type_html_allow'] =
$GLOBALS['phpAds_hlp_type_txt_allow'] = "\n        ".MAX_PRODUCT_NAME." siap untuk mengolah jenis banner yang berbeda dengan cara\n		yang berbeda. Penyetelan pertama dan kedua digunakan untuk penyimpanan banner\n		secara lokal. Silakan gunakan Interface Administrator untuk meng-upload banner,\n		".MAX_PRODUCT_NAME." akan menyimpan banner tersebut dalam database SQL atau\n		di web server. Menyimpan banner di sebuah web server eksternal diperbolehkan,\n		silakan gunakan HTML atau teks biasa untuk membuat banner.\n		";

$GLOBALS['phpAds_hlp_type_web_mode'] = "\n	Bila Anda ingin menggunakan banner yang disimpan pada web server, penyetelan disini perlu\n		dilakukan. Untuk simpan banner dalam direktori lokal penyetelan ini harus ditetapkan\n		pada posisi <i>Direktori Lokal</i>. Bila Anda ingin simpan banner pada FTP\n		Server eksternal, penyetelan ini harus berada pada posisi <i>FTP Server\n		Eksternal</i>. Pada web server tertentu Anda ingin menggunakan penyimpanan FTP,\n		meskipun di web server lokal.\n		";

$GLOBALS['phpAds_hlp_type_web_dir'] = "\n	Menetapkan direktori yang akan digunakan oleh ".MAX_PRODUCT_NAME." untuk meng-upload\n		banner. PHP harus diberi izin untuk menulis dalam direktori tersebut yang berarti,\n		Anda kemungkinan perlu ubah hak Unix (chmod) untuk direktori ini. Direktori yang\n		ditepatkan disini harus terletak dalam <i>Document Root</i> sehubungan web server\n		harus melayani file-file yang bersangkutan secara langsung. Tidak diperbolehkan\n		tanda <i>Slash</i> (/) di ujung. Anda diharuskan untuk mengkonfigurasikan\n		fungsi ini bila metode penyimpanan yang digunakan di <i>Direktori Lokal</i>.\n		";

$GLOBALS['phpAds_hlp_type_web_ftp_host'] = "\n	Bila Anda menetapkan metode penyimpanan pada <i>Server FTP Eksternal</i>, Anda perlu\n		tepatkan alamat IP atau nama domain dari Server FTP Eksternal tersebut, yang akan\n		digunakan oleh ".MAX_PRODUCT_NAME." untuk meng-upload banner.\n		";

$GLOBALS['phpAds_hlp_type_web_ftp_path'] = "\n	Bila Anda menetapkan metode penyimpanan pada <i>Server FTP Eksternal</i>, Anda perlu\n		tepatkan sebuah direktori dari Server FTP Eksternal tersebut, yang akan digunakan\n		oleh ".MAX_PRODUCT_NAME." untuk meng-upload banner.\n		";

$GLOBALS['phpAds_hlp_type_web_ftp_user'] = "\n	Bila Anda menetapkan metode penyimpanan pada <i>Server FTP Eksternal</i>, Anda perlu\n		berikan nama pengguna yang akan digunakan oleh ".MAX_PRODUCT_NAME." untuk buka\n		koneksi ke Server FTP Eksternal yang bersangkutan.\n		";

$GLOBALS['phpAds_hlp_type_web_ftp_password'] = "\n	Bila Anda menetapkan metode penyimpanan pada <i>Server FTP Eksternal</i>, Anda perlu\n		berikan kata sandi yang akan digunakan oleh ".MAX_PRODUCT_NAME." untuk buka\n		koneksi ke Server FTP Eksternal yang bersangkutan.\n		";

$GLOBALS['phpAds_hlp_type_web_url'] = "\n	Bila Anda menyimpan banner dalam web server ".MAX_PRODUCT_NAME." perlu diberitahui,\n		URL umum mana yang berkorespondensi dengan direkori yang ditepatkan dibawah ini.\n		Tidak diperbolehkan tanda <i>Slash</i> (/) di ujung URL yang ditepatkan disini.\n		";

$GLOBALS['phpAds_hlp_type_html_auto'] = "\n	Bila fungsi ini ditepatkan pada posisi ON, ".MAX_PRODUCT_NAME." akan mengubah banner\n		HTML guna mencatat Clicks pada banner tersebut. Meskipun fasilitas ini berada dalam\n		posisi On, Anda tetap diperbolehkan untuk tentukannya atas dasar per banner.\n		";

$GLOBALS['phpAds_hlp_type_html_php'] = "\n	".MAX_PRODUCT_NAME." memungkinkan untuk mengeksekusi kode PHP yang terletak dalam\n		banner HTML. Fungsi ini ditepatkan dalam posisi OFF secara default.\n		";

$GLOBALS['phpAds_hlp_admin'] = "\n	Silakan isi nama pengguna dari Administrator. Dengan nama pengguna tersebut Anda\n		diperbolehkan untuk me-login ke Interface Administrator.\n		";

$GLOBALS['phpAds_hlp_admin_pw'] =
$GLOBALS['phpAds_hlp_admin_pw2'] = "\n	Silakan ketik kata sandi yang ingin digunakan untuk me-login ke Interface Administrator.\n		Kata sandi perlu diketik berulang dua kali untuk menghindar kekeliruan pengetetikan.\n		";

$GLOBALS['phpAds_hlp_pwold'] =
$GLOBALS['phpAds_hlp_pw'] =
$GLOBALS['phpAds_hlp_pw2'] = "\n	Untuk mengubah kata sandi dari Administrator, Anda perlu sebutkan kata sandi yang lama\n		diatas. Kata sandi yang baru perlu diketik berulang dua kali untuk hindari kekeliruan\n		sewaktu penggantian kata sandi.\n		";

$GLOBALS['phpAds_hlp_admin_fullname'] = "\n	Nama lengkap dari Administrator. Nama yang tercantum disini digunakan untuk mengirim statistik\n		melalui E-Mail.\n		";

$GLOBALS['phpAds_hlp_admin_email'] = "\n	Alamat E-Mail dari Administrator. Alamat ini digunakan sebagai alamat dari pengirim\n		setiap kalinya E-Mail tentang statistik dikirim.\n		";

$GLOBALS['phpAds_hlp_admin_email_headers'] = "\n	Anda diperbolehkan untuk mengubah header dari E-Mail yang dikirim oleh ".MAX_PRODUCT_NAME.".\n		";

$GLOBALS['phpAds_hlp_admin_novice'] = "\n	Bila Anda ingin menerima peringatan sebelum menghapus Pemasang Iklan, kampanye, banner,\n		penerbit dan zona tepatkan penyetelan ini ke <i>True</i>.\n		";

$GLOBALS['phpAds_hlp_client_welcome'] = "\n	Bila fasilitas ini ditepatkan pada posisi On, sebuah kabar Selamat Datang akan ditampilkan\n		pada halaman pembuka setelah Pemasang Iklan login. Anda diperbolehkan untuk ubah\n		kabar ini sesuai keinginan Anda dengan meng-edit file welcome.html yang terletak pada\n		direktori admin/templates. Mungkin Anda ingin menambahkan nama perusahaan, informasi\n		tentang alamat, lambang perusahaan, sebuah link ke halaman harga untuk beriklan di\n		situs Anda dll..\n		";

$GLOBALS['phpAds_hlp_client_welcome_msg'] = "\n	Daripada meng-edit file welcome.html, Anda diperbolehkan untuk mengisi kabar disini. Bila Anda\n		tulis teks disini, file welcome.html akan diabaikan. Diperbolehkan untuk menggunakan\n		<i>HTML Tags</i> disini.\n		";

$GLOBALS['phpAds_hlp_updates_frequency'] = "\n	Bila Anda ingin tahu apakah sudah tersedia versi baru dari ".MAX_PRODUCT_NAME.", fungsi ini\n		harus ditepatkan pada  posisi On. Diperbolehkan untuk menentukan jarak waktu yang berulang,\n		".MAX_PRODUCT_NAME." akan membuka koneksi ke update server tersendiri. Jika ditemukan\n		versi yang baru, sebuah kabar akan muncul untuk memberikan informasi tambahan tentang\n		update tersebut.\n		";

$GLOBALS['phpAds_hlp_userlog_email'] = "\n	Bila Anda ingin simpan salinan dari seluruh E-Mail yang dikirim oleh ".MAX_PRODUCT_NAME.",\n		fungsi ini harus ditepatkan pada posisi On. Seluruh E-Mail akan tersimpan dalam\n		<i>Userlog</i>.\n		";

$GLOBALS['phpAds_hlp_userlog_priority'] = "\n	Untuk memastikan bahwa kalkulasi prioriti sudah berjalan dengan baik, Anda bisa menyimpan laporan\n		tentang kalkulasi setiap jam. Laporan ini berisi ramalan profil dan jumlah prioriti yang\n		ditetapkan pada seluruh banner. Informasi ini bernilai jika Anda ingin mengirimkan sebuah\n		<i>Bugreport</i> tentang kalkulasi prioriti. Seluruh Laporan akan tersimpan dalam\n		<i>Userlog</i>.\n		";

$GLOBALS['phpAds_hlp_userlog_autoclean'] = "\n	Untuk memastikan bahwa database telah dipangkas secara benar, Anda bisa simpan sebuah laporan\n		tentang apa saja yang terjadi sewaktu pemangkasan tersebut dijalankan. Informasi ini\n		akan tersimpan pada Userlog.\n		";

$GLOBALS['phpAds_hlp_default_banner_weight'] = "\n	Bila Anda ingin menggunakan bobot banner yang lebih tinggi sebagai default, silakan tentukan bobot\n		yang diinginkan disini. Stelan 1 adalah penyetelan default.\n		";

$GLOBALS['phpAds_hlp_default_campaign_weight'] = "\n	Bila Anda ingin menggunakan bobot kampanye yang lebih tinggi sebagai default, silakan tentukan bobot\n		yang diinginkan disini. Stelan 1 adalah penyetelan default.\n		";

$GLOBALS['phpAds_hlp_gui_show_campaign_info'] = "\n	Bila penyetelan ini diaktifkan, sebuah informasi khusus tentang setiap kampanye akan ditampilkan\n		pada halaman <i>Ikhtisar dari Kampanye</i>. Informasi khusus tersebut berisi jumlah AdViews yang\n		tersisa, jumlah AdClicks yang tersisa, tanggal aktivasi, waktu berakhir dan penyetelan\n		prioritas.\n		";

$GLOBALS['phpAds_hlp_gui_show_banner_info'] = "\n	Bila penyetelan ini diaktifkan, sebuah informasi khusus tentang setiap banner akan ditampilkan\n		pada halaman <i>Pandangan Banner</i>. Informasi khusus tersebut berisi URL tujuan,\n		kata kunci, ukuran dan bobot dari banner-banner yang bersangkutan.\n		";

$GLOBALS['phpAds_hlp_gui_show_campaign_preview'] = "\n	Bila penyetelan ini diaktifkan, sebuah <i>Preview</i> dari semua banner akan ditampilkan pada halaman\n		<i>Pandangan Banner</i>. Bila penyetelan ini tidak aktif, sebuah <i>Preview</i> dari\n		seluruh banner tetap ditampilkan jika Anda men-klik segitiga yang berlokasi di sebelahnya\n		setiap banner pada halaman <i>Pandangan Banner</i>.\n		";

$GLOBALS['phpAds_hlp_gui_show_banner_html'] = "\n	Bila penyetelan ini diaktifkan, banner yang sebenarnya akan ditampilkan dan bukan kode HTML. Penyetelan\n		ini tidak aktif sebagai default, sehubungan banner HTML mampu untuk berkonflik dengan interface\n		dari pengguna. Bila penyetalan ini tidak aktif, banner yang sebenarnya tetap bisa dimunculkan\n		dengan cara menggunakan	tombol <i>Tampilkan Banner</i> yang terletak di sebelah kode HTML.\n		";

$GLOBALS['phpAds_hlp_gui_show_banner_preview'] = "\n	Bila penyetelan ini diaktifkan, sebuah <i>Preview</i> akan ditampilkan pada halaman <i>Properties dari\n		Banner</i>, <i>Pilihan Penyampaian</i> dan halaman <i>Zona yang di-link</i>. Bila penyetalan ini\n		tidak aktif, hal-hal yang tersembunyi tetap ditampilkan dengan cara menggunakan\n		tombol <i>Tampilkan Banner</i> yang terletak pada bagian atas dari halaman yang bersangkutan.\n		";

$GLOBALS['phpAds_hlp_gui_hide_inactive'] = "\n	Bila penyetelan ini diaktifkan, seluruh banner, kampanye dan Pemasang Iklan akan disembunyikan\n		dari halaman <i>Pemasang Iklan & Kampanye</i> dan dari halaman <i>Ikhtisar Kampanye</i>.\n		Bila penyetalan ini aktif, hal-hal yang tersembunyi tetap ditampilkan dengan cara menggunakan\n		tombol <i>Tampilkan Semua</i> yang terletak pada bagian bawah dari halaman yang bersangkutan.\n		";

$GLOBALS['phpAds_hlp_gui_show_matching'] = "\n	Bila pilihan ini diaktifkan banner sebanding akan tertampil pada halaman <i>Linked banners</i> jika\n		metode <i>Campaign selection</i> dipilihkan. Hal ini mengizinkan Anda untuk melihat secara\n		pasti banner yang mana saja ditentukan untuk disampaikan kalau sebuah kampanye di-link.\n		Memungkinkan juga untuk melihat banner sebanding dalam <i>Preview</i>.\n		";

$GLOBALS['phpAds_hlp_gui_show_parents'] = "\n	Bila pilihan ini diaktifkan kampanye induk dari banner akan tertampil pada halaman <i>Linked banners</i>\n		jika metode <i>Banner selection</i> dipilihkan. Hal ini mengizinkan Anda untuk memastikan banner\n		mana dimiliki oleh kampanye yang mana sebelum banner yang bersangkutan di-link. Hal ini juga berarti\n		bahwa banner dikelompokkan oleh kampanye induknya dan tidak lagi diurut bedasarkan abjad.\n		";

$GLOBALS['phpAds_hlp_gui_link_compact_limit'] = "\n	Secara Default seluruh banner dan kampanye yang tersedia ditampilkan pada halaman <i>Linked banners</i>.\n		Sehubungan begitu, halaman yang ditampilkan bisa menjadi besar sekali jika macam-macam banner\n		yang berbeda tersimpan pada inventori. Pilihan ini mengizinkan Anda untuk menetapkan jumlah maksimal\n		yang akan ditampilkan pada satu halaman. Bila jumlahnya dan caranya banner-banner di-link berbeda,\n		metode yang membutuhkan ruang paling sedikit akan digunakan.\n		";

?>
