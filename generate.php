<?php
require_once 'vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bulan = $_POST['bulan'] ?? '';
    $tahun = $_POST['tahun'] ?? '';
    $tanggalHari = [];
    $kegiatanData = [];

    if (!empty($bulan) && !empty($tahun)) {
        $jumlahHari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

        for ($i = 1; $i <= $jumlahHari; $i++) {
            $tanggal = sprintf('%04d-%02d-%02d', $tahun, $bulan, $i);
            $hari = ucfirst(jadiHari(date('N', strtotime($tanggal))));
            $tanggalHari[] = "$hari, " . date('d', strtotime($tanggal)) . ' ' . getNamaBulan($bulan) . ' ' . $tahun;

            $inputKegiatan = $_POST['kegiatan'][$i] ?? '';
            if (is_array($inputKegiatan)) {
                $cleaned = array_map('trim', $inputKegiatan);
                $cleaned = array_filter($cleaned, fn($v) => $v !== '');
                $kegiatanData[$i] = $cleaned;
            } else {
                $uraian = trim($inputKegiatan);
                $kegiatanData[$i] = $uraian !== '' ? [$uraian] : ['-'];
            }
        }

        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(10);

        $section = $phpWord->addSection();

        $section->addText('LAPORAN KEGIATAN HARIAN PEGAWAI', ['bold' => true], ['alignment' => 'center']);
        $section->addText('TVRI STASIUN LAMPUNG TAHUN ' . $tahun, ['bold' => true], ['alignment' => 'center']);
        $section->addTextBreak(1);

        $infoTable = $section->addTable();
        $infoTable->addRow();
        $infoTable->addCell(2000)->addText('Nama');
        $infoTable->addCell()->addText(': Muhammad Junaedi');
        $infoTable->addRow();
        $infoTable->addCell()->addText('NIP');
        $infoTable->addCell()->addText(': 19720607 201409 1 002');
        $infoTable->addRow();
        $infoTable->addCell()->addText('Jabatan');
        $infoTable->addCell()->addText(': Penata Dekorasi/Staf Program dan PU');
        $infoTable->addRow();
        $infoTable->addCell()->addText('Bulan');
        $infoTable->addCell()->addText(': ' . getNamaBulan($bulan) . ' ' . $tahun);
        $section->addTextBreak(1);

        $tableStyle = [
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => 50,
        ];

        $table = $section->addTable($tableStyle);

        $table->addRow();
        $table->addCell(1000, $tableStyle)->addText('No', ['bold' => true], ['alignment' => 'center']);
        $table->addCell(4000, $tableStyle)->addText('Tanggal', ['bold' => true], ['alignment' => 'center']);
        $table->addCell(3000, $tableStyle)->addText('Jam', ['bold' => true], ['alignment' => 'center']);
        $table->addCell(6000, $tableStyle)->addText('Uraian Kegiatan', ['bold' => true], ['alignment' => 'center']);

        $boldKeywords = [
            'Sudut Pandang', 'Serambi Iman', 'Ayo Mengaji', 'Kesehatan',
            'Anak Ceria', 'Musik Kita', 'PLAYBACK', 'CUTI', 'OFF'
        ];

        for ($i = 1; $i <= $jumlahHari; $i++) {
            $table->addRow();
            $table->addCell(null, $tableStyle)->addText((string)$i, [], ['alignment' => 'center']);
            $table->addCell(null, $tableStyle)->addText($tanggalHari[$i - 1], [], ['alignment' => 'center']);
            $table->addCell(null, $tableStyle)->addText('08.00 - 17.00', [], ['alignment' => 'center']);

            $uraianList = $kegiatanData[$i] ?? ['-'];
            $cell = $table->addCell(null, $tableStyle);

            foreach ($uraianList as $item) {
                $foundKeyword = null;
                foreach ($boldKeywords as $keyword) {
                    if (stripos($item, $keyword) !== false) {
                        $foundKeyword = $keyword;
                        break;
                    }
                }

                if ($foundKeyword) {
                    $parts = explode($foundKeyword, $item, 2);
                    $textrun = $cell->addTextRun();
                    $textrun->addText("- ");
                    $textrun->addText($parts[0]);
                    $textrun->addText($foundKeyword, ['bold' => true]);
                    $textrun->addText($parts[1] ?? '');
                } else {
                    $cell->addText("- " . $item);
                }
            }
        }

        $section->addTextBreak(2);
        $firstMonday = getFirstMonday($tahun, $bulan);
        $section->addText("Bandar Lampung, $firstMonday", null, ['alignment' => 'right']);

        $footerTable = $section->addTable();
        $footerTable->addRow();
        $footerTable->addCell(4000)->addText("Yang Melaksanakan");
        $footerTable->addCell(4000)->addText("Mengetahui");
        $footerTable->addRow();
        $footerTable->addCell()->addTextBreak(2);
        $footerTable->addCell()->addTextBreak(2);
        $footerTable->addRow();
        $footerTable->addCell()->addText("Muhammad Junaedi", ['bold' => true]);
        $footerTable->addCell()->addText("Jonizar, S.Sos., MM", ['bold' => true]);
        $footerTable->addRow();
        $footerTable->addCell()->addText("NIP. 19720607 201409 1 002");
        $footerTable->addCell()->addText("NIP. 19711202 199303 1 002");

        $section->addTextBreak(1);
        $section->addText("Menyetujui", null, ['alignment' => 'center']);
        $section->addText("Kepala TVRI Stasiun Lampung", null, ['alignment' => 'center']);
        $section->addTextBreak(2);
        $section->addText("Muhammad Ikhsan, S.T., M.T.", ['bold' => true, 'underline' => 'single'], ['alignment' => 'center']);
        $section->addText("NIP. 19721019 199903 1 003", null, ['alignment' => 'center']);

        $fileName = "Laporan Kegiatan Harian Pegawai " . getNamaBulan($bulan) . " $tahun - M Junaedi.docx";
        $tempFile = tempnam(sys_get_temp_dir(), 'PHPWord');
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($tempFile);

        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
        header("Content-Transfer-Encoding: binary");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Expires: 0");
        readfile($tempFile);
        unlink($tempFile);
        exit;
    }
}

function jadiHari($angka) {
    $namaHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
    return $namaHari[$angka - 1] ?? 'Tidak diketahui';
}

function getNamaBulan($bulan) {
    $bulanList = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
        '04' => 'April', '05' => 'Mei', '06' => 'Juni',
        '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
        '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];
    $bulan = str_pad($bulan, 2, '0', STR_PAD_LEFT);
    return $bulanList[$bulan] ?? 'Tidak diketahui';
}

function getFirstMonday($tahun, $bulan) {
    for ($i = 1; $i <= 7; $i++) {
        $tanggal = date("Y-m-d", strtotime("$tahun-$bulan-$i"));
        if (date('N', strtotime($tanggal)) == 1) {
            return date('d', strtotime($tanggal)) . ' ' . getNamaBulan($bulan) . ' ' . $tahun;
        }
    }
    return '01 ' . getNamaBulan($bulan) . ' ' . $tahun;
}
