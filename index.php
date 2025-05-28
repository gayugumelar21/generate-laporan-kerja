<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Laporan Kegiatan Harian</title>
  <style>
    body {
      font-family: 'Times New Roman', serif;
      font-size: 12pt;
      background: linear-gradient(to right, #f4f4f4, #e0e0e0);
      margin: 0;
      padding: 10px;
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    form {
      background: #ffffff;
      border-radius: 8px;
      padding: 15px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      max-width: 100%;
      box-sizing: border-box;
    }

    label {
      font-weight: bold;
      display: inline-block;
      margin-top: 10px;
    }

    select {
      padding: 5px;
      font-size: 12pt;
      font-family: 'Times New Roman', serif;
      margin-top: 5px;
      margin-right: 10px;
    }

    input[type=submit], input[type=button] {
      margin-top: 20px;
      padding: 10px 20px;
      font-size: 12pt;
      font-family: 'Times New Roman', serif;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      width: 100%;
    }

    input[type=submit]:hover, input[type=button]:hover {
      background-color: #0056b3;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      overflow-x: auto;
      display: block;
    }

    th, td {
      border: 1px solid #000;
      padding: 8px;
      text-align: center;
      vertical-align: top;
      word-wrap: break-word;
    }

    th {
      background-color: #dcdcdc;
    }

    .checkbox-group {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      text-align: left;
    }

    .checkbox-group label {
      font-weight: normal;
      font-size: 11pt;
    }

    /* Responsif untuk layar kecil */
    @media (max-width: 768px) {
      form {
        padding: 10px;
      }

      table {
        font-size: 10pt;
      }

      input[type=submit], input[type=button] {
        font-size: 11pt;
      }

      .checkbox-group label {
        font-size: 10pt;
      }

      label, select {
        display: block;
        width: 100%;
      }

      select {
        margin-bottom: 10px;
      }
    }
  </style>
</head>
<body>

  <h2>Laporan Kegiatan Harian Pegawai</h2>

  <form action="generate.php" method="POST" id="laporanForm">
    <label for="bulan">Bulan:</label>
    <select name="bulan" id="bulan" required>
      <?php
        $bulanList = [
          "Januari", "Februari", "Maret", "April", "Mei", "Juni",
          "Juli", "Agustus", "September", "Oktober", "November", "Desember"
        ];
        foreach ($bulanList as $index => $bln) {
          echo "<option value='" . ($index + 1) . "'>$bln</option>";
        }
      ?>
    </select>

    <label for="tahun">Tahun:</label>
    <select name="tahun" id="tahun" required>
      <?php
        $tahunNow = date("Y");
        for ($i = $tahunNow; $i >= 2020; $i--) {
          echo "<option value='$i'>$i</option>";
        }
      ?>
    </select>

    <input type="button" onclick="generateRows()" value="Tampilkan Form">

    <div id="tableContainer"></div>

    <input type="submit" value="Generate Word" style="display:none;" id="submitBtn">
  </form>

  <script>
    function getJumlahHari(bulan, tahun) {
      return new Date(tahun, bulan, 0).getDate();
    }

    function generateRows() {
      const bulan = document.getElementById("bulan").value;
      const tahun = document.getElementById("tahun").value;
      const hari = getJumlahHari(bulan, tahun);
      const container = document.getElementById("tableContainer");

      let html = `
        <table>
          <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Jam</th>
            <th>Uraian Kegiatan</th>
          </tr>
      `;

      for (let i = 1; i <= hari; i++) {
        let tanggalFormatted = tahun + '-' + String(bulan).padStart(2, '0') + '-' + String(i).padStart(2, '0');
        html += `
          <tr>
            <td>${i}</td>
            <td>
              <input type="date" name="tanggal[${i}]" value="${tanggalFormatted}" required readonly>
            </td>
            <td>
              <input type="hidden" name="jam_mulai[${i}]" value="08.00">
              <input type="hidden" name="jam_selesai[${i}]" value="17.00">
              08.00 - 17.00
            </td>
            <td>
              <div class="checkbox-group">
                <label><strong><input type="checkbox" name="kegiatan[${i}][]" value="CUTI"> CUTI</strong></label>
                <label><strong><input type="checkbox" name="kegiatan[${i}][]" value="OFF"> OFF</strong></label>
                <label><strong><input type="checkbox" name="kegiatan[${i}][]" value="PLAYBACK"> PLAYBACK</strong></label>
                <label><input type="checkbox" name="kegiatan[${i}][]" value="Koordinasi dengan Pengarah Acara terkait dekorasi acara Sudut Pandang"> Koordinasi dengan Pengarah Acara terkait dekorasi acara Sudut Pandang</label>
                <label><input type="checkbox" name="kegiatan[${i}][]" value="Melakukan Setting dan bongkar dekorasi acara Sudut Pandang"> Melakukan Setting dan bongkar dekorasi acara Sudut Pandang</label>
                <label><input type="checkbox" name="kegiatan[${i}][]" value="Koordinasi dengan Pengarah Acara terkait dekorasi acara Serambi Iman"> Koordinasi dengan Pengarah Acara terkait dekorasi acara Serambi Iman</label>
                <label><input type="checkbox" name="kegiatan[${i}][]" value="Melakukan Setting dan bongkar dekorasi acara Serambi Iman"> Melakukan Setting dan bongkar dekorasi acara Serambi Iman</label>
                <label><input type="checkbox" name="kegiatan[${i}][]" value="Koordinasi dengan Pengarah Acara terkait dekorasi acara Ayo Mengaji"> Koordinasi dengan Pengarah Acara terkait dekorasi acara Ayo Mengaji</label>
                <label><input type="checkbox" name="kegiatan[${i}][]" value="Melakukan Setting dan bongkar dekorasi acara Ayo Mengaji"> Melakukan Setting dan bongkar dekorasi acara Ayo Mengaji</label>
                <label><input type="checkbox" name="kegiatan[${i}][]" value="Koordinasi dengan Pengarah Acara terkait dekorasi acara Kesehatan"> Koordinasi dengan Pengarah Acara terkait dekorasi acara Kesehatan</label>
                <label><input type="checkbox" name="kegiatan[${i}][]" value="Melakukan Setting dan bongkar dekorasi acara Kesehatan"> Melakukan Setting dan bongkar dekorasi acara Kesehatan</label>
                <label><input type="checkbox" name="kegiatan[${i}][]" value="Koordinasi dengan Pengarah Acara terkait dekorasi acara Anak Ceria"> Koordinasi dengan Pengarah Acara terkait dekorasi acara Anak Ceria</label>
                <label><input type="checkbox" name="kegiatan[${i}][]" value="Melakukan Setting dan bongkar dekorasi acara Anak Ceria"> Melakukan Setting dan bongkar dekorasi acara Anak Ceria</label>
                <label><input type="checkbox" name="kegiatan[${i}][]" value="Koordinasi dengan Pengarah Acara terkait dekorasi acara Musik Kita"> Koordinasi dengan Pengarah Acara terkait dekorasi acara Musik Kita</label>
                <label><input type="checkbox" name="kegiatan[${i}][]" value="Melakukan Setting dan bongkar dekorasi acara Musik Kita"> Melakukan Setting dan bongkar dekorasi acara Musik Kita</label>
              </div>
            </td>
          </tr>
        `;
      }

      html += `</table>`;
      container.innerHTML = html;
      document.getElementById("submitBtn").style.display = 'inline-block';
    }
  </script>

</body>
</html>
