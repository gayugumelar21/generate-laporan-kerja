$section->addTextBreak(2);
$section->addText("Bandar Lampung, " . date("d F Y"), null, ['alignment' => 'right']);

$footerTable = $section->addTable();

// Baris pertama
$footerTable->addRow();
$footerTable->addCell(4000)->addText("Yang Melaksanakan", ['underline' => 'single']);
$footerTable->addCell(4000)->addText("Mengetahui", ['underline' => 'single']);

// Baris kedua
$footerTable->addRow();
$footerTable->addCell()->addTextBreak(2); // Jarak tanda tangan
$footerTable->addCell()->addTextBreak(2);

// Baris ketiga (nama)
$footerTable->addRow();
$footerTable->addCell()->addText("Muhammad Junaedi", ['bold' => true]);
$footerTable->addCell()->addText("Jonizar, S.Sos., MM", ['bold' => true]);

// Baris keempat (NIP)
$footerTable->addRow();
$footerTable->addCell()->addText("NIP. 19720607 201409 1 002");
$footerTable->addCell()->addText("NIP. 19711202 199303 1 002");

// Baris kelima (menyetujui)
$section->addTextBreak(1);
$section->addText("Menyetujui", ['underline' => 'single'], ['alignment' => 'center']);
$section->addText("Kepala TVRI Stasiun Lampung", null, ['alignment' => 'center']);
$section->addTextBreak(2);
$section->addText("Muhammad Ikhsan, S.T., M.T.", ['bold' => true, 'underline' => 'single'], ['alignment' => 'center']);
$section->addText("NIP. 19721019 199903 1 003", null, ['alignment' => 'center']);
