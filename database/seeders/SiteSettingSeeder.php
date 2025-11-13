<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SiteSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only insert default site settings if the table is empty to avoid duplicates
        if (DB::table('site_settings')->count() > 0) {
            return;
        }

        DB::table('site_settings')->insert([
            'site_name' => 'Raja Kantor',
            'logo' => 'images/logo-old.jpg',
            'favicon' => 'images/favicon.ico',
            'meta_title' => 'Jual Meja Kantor - Jual Kursi Kantor - Distributor Meja Kursi Kantor',
            'meta_description' => 'Distributor kursi kantor dan meja kantor, brankas, meja gambar dan mesin absensi serta peralatan kantor lainnya, dengan mutu terbaik dari rajakantor.com',
            'meta_keywords' => 'jual kursi kantor, jual meja kantor, kursi dan meja kantor, jual lemari arsip',
            'about' => '<p><span style="font-size: medium;">Puji sukur kita panjatkan kepada Tuhan Yang Maha Esa atas segala Rahmat Nya</span></p>
<p><span style="font-size: medium;">Semenjak tahun 2006 kami hadir di dunia online, dan mulai dari hari itu kami selalu menjaga kepercayaan konsumen seluruh Indonesia untuk mengadakan pengadaan furniture kantor, baik untuk instansi pemerintahan ataupun untuk pengadaan furniture kantor di perusahaan swasta.</span></p>
<p><span style="font-size: medium;">Dan sebagai distributor atau agen furniture dan alat kantor di seluruh indonesia. Kami menyediakan berbagai macam kebutuhan alat kantor di toko kami secara online di www.rajakantor.com seperti : <strong>Meja Kantor, Kursi Kantor, Filling Cabinet, Lemari Arsip, Loker, Brankas, Partisi Kantor, Mesin Absensi, Mobile File, Mesin Penghancur Kertas, Mesin Gambar, dan sebagainya.</strong></span></p>
<p><span style="font-size: medium;"><br></span></p>
<p><strong><span style="font-size: medium;">&nbsp;</span></strong></p>
<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow: hidden;"><strong><span style="font-size: medium;">Disini kami memberikan produk-produk terbaik dari berbagai merk ternama dan tentunya sudah tersertifikat untuk memenuhi kebutuhan kantor anda seperti : Daiko, Elite, Lion, Alba, Modera, Uno, Chairman, Savello, Subaru, Ichiban, Daichiban, Bofa, dan lain nya. Kami bergerak dalam pengadaan kantor sejak tahun 1990 dan memenuhi konsumen kami dari seluruh indonesia seperti : Bandung, Banjarmasin, Bali, Jakarta, Lampung, Lombok, Medan, Makassar, Manado, Padang, Palembang, Pekanbaru, Pontianak, Papua, Semarang, Surabaya, Samarinda, Yogyakarta, dan kota-kota besar lain nya.</span></strong></div>
<p><strong><span style="font-size: medium;"> </span></strong></p>
<div id="_mcePaste" style="position: absolute; left: -10000px; top: 0px; width: 1px; height: 1px; overflow: hidden;"><strong><span style="font-size: medium;">Kami Raja Kantor, selalu memberikan harga yang murah dengan diskon yang kami berikan kepada anda sebagai pembeli dan kami akan selalu memberikan produk terbaik sesuai dengan kebutuhan anda. Sebelum nya kami mengucapkan terimakasih kepada konsumen kami yang telah mempercayakan Raja Kantor sebagai toko dalam memenuhi kebutuhan kantor anda</span></strong></div>
<p><span style="font-size: medium;">Disini kami memberikan produk-produk terbaik dari berbagai merk ternama dan tentunya sudah tersertifikat untuk memenuhi kebutuhan kantor anda seperti : <strong>Daiko, Elite, Lion, Alba, Modera, Uno, Chairman, Savello, Subaru, Ichiban, Daichiban, Bofa, dan lain nya</strong>. Kami bergerak dalam pengadaan kantor sejak tahun 1990 dan memenuhi konsumen kami dari seluruh indonesia seperti : Bandung, Banjarmasin, Bali, Jakarta, Lampung, Lombok, Medan, Makassar, Manado, Padang, Palembang, Pekanbaru, Pontianak, Papua, Semarang, Surabaya, Samarinda, Yogyakarta, dan kota-kota besar lain nya.</span></p>
<p><span style="font-size: medium;"><br></span></p>
<p><span style="font-size: medium;">Kami Raja Kantor, selalu memberikan harga yang murah dengan diskon yang kami berikan kepada anda sebagai pembeli dan kami akan selalu memberikan produk terbaik sesuai dengan kebutuhan anda. Sebelum nya kami mengucapkan terimakasih kepada konsumen kami yang telah mempercayakan Raja Kantor sebagai toko dalam memenuhi kebutuhan kantor anda.</span></p>
<p>&nbsp;</p>
<p><span style="font-size: medium;">salam hangat,</span></p>
<p>&nbsp;</p>
<p><span style="font-size: medium;">Dedi Surachman</span></p>',
            'information' => '
        <p style=""><strong>Raja Kantor</strong><br>
            Jl. Otista Raya No 143, cawang 13330<br>
            Jakarta Timur<br>
            Tlp : 021 857 0831           
        </p><ul style="margin-left:36px;"> 
            <li>021 857 0832</li> 
            <li>021 857 0833 </li> 
            <li>021 857 0834 </li> 
            <li>0816 136 0607</li> 
            <li>0877 8199 9910</li> 
            <li>0821 2222 0503</li>
        </ul>

        <p style="">WA : 082122220503 </p>
        <p style="">Fax : 021 857 0830 </p>
        <p style="">E-mail : info@rajakantor.com</p>
        <p style=""><a href="https://goo.gl/maps/tmCTW5NeRVQ2">Lihat Peta</a></p>
        <hr>
        <p style="">
            <strong>Raja Kantor Surabaya</strong><br>
            Jl. Rungkut Mejoyo 2 no 23,<br>
            Kalirungkut (samping kampus UBAYA),<br>
            SURABAYA<br>
        </p>
         <ul style="margin-left:36px;"> 
            <li>031 8479 257</li>
            <li>0878 5312 0306</li>
            <li>0822 4592 3208</li>
         </ul>
        <p style="">Fax : 031 847 9257</p>
        <p style="">E-mail : rajakantorsurabaya@yahoo.com</p>
        <hr>
         <p style="">
            <strong>Raja Kantor Bandung</strong><br>
            Jl. Terusan Buah Batu,<br>
            Cipagalo, No 307 (dekat pintu tol buah batu),<br>
            BANDUNG<br>
        </p>
        <ul style="margin-left:36px;"> 
            <li>0878 1123 4343</li>
            <li>0822 1003 0307</li>
            <li>022 8752 9842</li>
         </ul>
         <p style="">Fax : 022 8752 9842</p>
        <hr>
         <p style="">
            <strong>Raja Kantor Semarang</strong><br>
            Jl. Wolter Monginsidi No.8,<br>
            ( samping golden futsal )
             Pedurungan Tengah, Kec. Pedurungan, Kota Semarang,<br>
             Jawa Tengah 50192<br>
        </p>
          
        <ul style="margin-left:36px;"> 
            <li>081901435351</li>
            <li>081222313154</li>
         </ul>
         <p style="">Fax : 024 7641 3369</p>
        <hr>
         <p style="">
            <strong>Raja Kantor Bogor</strong><br>
            Jl. KH. Abdullah Bin Nuh kota bogor, Jawa Barat<br>
        </p>
        <ul style="margin-left:36px;"> 
            <li>0813 2222 6181</li>
            <li>0877 819999 11</li>
         </ul>
         <p style="">Email : pusatalatkantor@yahoo.com</p>
        <hr>
         <p style="">
            <strong>Raja Kantor Makassar</strong><br>
            Coming Soon<br>
        </p>
        <ul style="margin-left:36px;"> 
            <li>0821 2222 0503</li>
            <!--<li>0878 7833 4455</li>-->
         </ul>
         <p style="">Email : info@rajakantor.com</p>
        <hr>
        <p style="">
            <strong>Raja Kantor Bali</strong><br>
            Coming Soon<br>
            Denpasar
        </p>
        <ul style="margin-left:36px;"> 
            <li>0821 2222 0503</li>
            <!--<li>0878 7833 4455</li>-->
         </ul>
         <p style="">Email : info@rajakantor.com</p>
        <hr>
        <p style="">
          
            <strong>Available at</strong><br>
            <a href="https://www.bukalapak.com/u/pusatalatkantor">
            <img src="https://www.rajakantor.com/images/bukalapak_logo.png" width="160">
            </a>
            <a href="https://www.tokopedia.com/rajakantor">
            <img src="https://www.rajakantor.com/images/tokopedia_logo.png" width="160">
            </a>
        </p>',
            'wa' => '628161360607',
            'wa_order' => '628161360607',
            'slider' => json_encode(['images/slide/slider1.png', 'images/slide/slider2.png', 'images/slide/slider3.png']),
            'banner_sidebar' => 'images/banner/LAYANAN-PENGADUAN.jpg',
            'banner_home_top' => 'images/banner/BANNER WEB 17.jpg',
            'banner_home_bottom' => 'images/banner/bannernew.jpeg',
            'terms' => null,
            'client' => '<ul>
<li>Global Islamic School, Jakarta</li>
<li>PT. Citra Nusantara Gemilang, Cikarang</li>
<li>PT. Geoprolog, Jakarta</li>
<li>PT. Indonesia Epson Industry</li>
<li>PT. Jababeka Infrastruktur</li>
<li>PT. Bayu Buana Gemilang</li>
<li>PT. Mutiara, Irian Jaya</li>
<li>Palembang</li>
<li>Cirebon</li>
<li>Pekalongan</li>
<li>Pekanbaru</li>
<li>Jambi</li>
<li>Lampung</li>
<li>Bali</li>
<li>Surabaya</li>
<li>Yogjakarta</li>
<li>Lamongan</li>
<li>Malang</li>
<li>Solo</li>
<li>Tasikmalaya</li>
<li>Cilegon</li>
<li>Sukabumi</li>
<li>Depok</li>
<li>Bogor</li>
<li>Aceh</li>
<li>Bengkulu</li>
<li>Pontianak</li>
<li>Manado</li>
<li>Tangerang</li>
<li>Samarinda</li>
<li>Balikpapan</li>
<li>Demak</li>
<li>Kudus</li>
<li>Dan Masih Banyak Lagi</li>
</ul>',
            'home_description' => '
    <h5 style="margin:10px 0;">
        WELCOME
    </h5>
    <h4>RAJA KANTOR</h4>
    <p style="padding: 0; margin:10px 0;">Pengunjung kami yang terhormat...</p>
    <p style="padding: 0; margin:10px 0; text-align: justify;">Senang sekali dapat bertemu dengan anda, dan terima kasih atas kunjungan anda di Web site kami, melalui Situs ini saya berharap kami dapat memenuhi semua kebutuhan Funuture seperti 
    <b><span style="color:red;">Meja Gambar, Meja Kantor, Mobile File Lemari Arsip, Brankas yang anda</span></b> inginkan dan harapkan, sesuai rencana dan budget anda.</p>
    <p style="padding: 0; margin:10px 0; text-align: justify;">
        Semoga melalui Situs ini kami dapat mempermudah anda untuk memperoleh informasi mengenai office equipment &amp; furniture Indonesia dimanapun anda berada dan sekaligus dapat terus mendekatkan diri kepada anda
    </p>
    <p style="padding: 0; margin:10px 0; text-align: justify;">
        Sebagai penutup ijinkan saya atas nama <b><span style="color:red;">RAJA KANTOR</span></b> mengucapkan terima kasih yang sebesar-besarnya atas kunjungan dan kepercayaan anda juga kepada seluruh suplayer
        yang terus mendukung kami selama ini.
    </p>
    <br>
    <p style="padding: 0; margin:10px 0; text-align: justify;">
        Dan semoga kesuksesan menyertai kita, dari tahun ke tahun.
    </p>',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
