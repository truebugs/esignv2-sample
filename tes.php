<?php
include "EsignService.php";

$esign = new EsignService();


################## verifikasi user ###############
// echo $esign->verifyUser("0803202100007062");
################## verifikasi user ###############


################## verifikasi pdf ###############

// echo $esign->verifyPdf(["file"=>"hasiltte/toyyib_2024.pdf"]);

################## verifikasi pdf ###############


################## sign pdf ###############

echo $esign->signPdf([
    'user'=>[
        'nik'=>'0803202100007062', // nik terfdaftar
        'passphrase'=> 'Hantek1234.!',
    ],
    'savetodir'=> 'hasiltte', //Pastikan direktori exists
    'savetofile'=> 'toyyib_2024.pdf', //sesuai kebutuhan wajib diakhir .pdf
    'file_pdf'=> 'contohfile/contohnota.pdf', // path file yang ingin di tte,
    'gambar_tte'=> 'contohfile/tte.jpg', // visual tte
    'halaman'=>1,
    'originX' => 40,
    'originY' => 40,
    'width' => 200,
    'height' => 100
]);
################## sign pdf ###############
