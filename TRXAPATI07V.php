<?php
include "conf/config.php";

// Array mapping untuk tipe pembayaran (Lebih bersih daripada if-else berjejer)
$payment_types = [
    'U' => 'Umum',
    'B' => 'BPJS',
    'A' => 'Asuransi',
    'P' => 'Perusahaan',
    'H' => 'Halodoc'
];
?>

<link rel="stylesheet" href="assets/css/modern-table.css">
<div class="table-wrapper">
    <table class="modern-table">
        <thead>
            <tr>
                <th>Tgl Daftar</th>
                <th>No. Antrian</th>
                <th>Poli</th>
                <th>Nama Pasien</th>
                <th>Pembayaran</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Ambil input pencarian dengan aman
            $kata = isset($_POST['q']) ? trim($_POST['q']) : '';

            // Optimasi Query menggunakan LEFT JOIN (Bukan Subquery di SELECT)
            $sql = "SELECT 
                        r.TRXA_REGI_CODE, 
                        r.TRXA_PATI_CODE,
                        r.TRXA_ENTR_DATE,
                        r.TRXA_REGI_LIST, 
                        r.TRXA_REGI_PAYM, 
                        r.TRXA_REGI_STAT,
                        r.TRXA_ENTR_TIME,
                        CONCAT(p.PATI_MAIN_TITL, ' ', p.PATI_MAIN_NAME) AS PATI_NAME,
                        pl.TBLA_POLI_NAME AS POLI_NAME,
                        (SELECT COUNT(*) FROM trxaexam e WHERE e.TRXA_EXAM_CODE = r.TRXA_REGI_CODE) AS SUDAH_PERIKSA
                    FROM trxaregi r
                    LEFT JOIN patimast p ON p.PATI_MAST_CODE = r.TRXA_PATI_CODE
                    LEFT JOIN tblapoli pl ON pl.TBLA_POLI_CODE = r.TRXA_REGI_POLI
                    WHERE r.TRXA_REGI_STAT = 'W' 
                      AND r.TRXA_VIEW_STAT = 'Y' 
                      AND DATE(r.TRXA_ENTR_DATE) >= CURDATE() - INTERVAL 2 DAY";

            // Modifikasi query berdasarkan input pencarian
            if (!empty($kata)) {
                $sql .= " AND (r.TRXA_PATI_CODE LIKE :kata OR p.PATI_MAIN_NAME LIKE :kata)
                          ORDER BY r.TRXA_REGI_LIST";
            } else {
                $sql .= " ORDER BY r.TRXA_ENTR_DATE DESC, r.TRXA_ENTR_TIME DESC";
            }

            try {
                // Gunakan Prepared Statement untuk mencegah SQL Injection
                $stmt = $db->prepare($sql);

                if (!empty($kata)) {
                    $stmt->bindValue(':kata', "%$kata%", PDO::PARAM_STR);
                }

                $stmt->execute();

                // Looping Data
                while ($k = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $regicode = htmlspecialchars($k['TRXA_REGI_CODE'] ?? '');
                    $tgl_daftar = htmlspecialchars($k['TRXA_ENTR_DATE'] ?? '');
                    $waktu_daftar = htmlspecialchars($k['TRXA_ENTR_TIME'] ?? '');
                    $no_antrian = htmlspecialchars($k['TRXA_REGI_LIST'] ?? '');
                    $poli_name = htmlspecialchars($k['POLI_NAME'] ?? '-');
                    $pati_name = htmlspecialchars($k['PATI_NAME'] ?? '-');

                    // Ambil string pembayaran dari array mapping
                    $xregipaym = $k['TRXA_REGI_PAYM'] ?? '';
                    $regipaym = $payment_types[$xregipaym] ?? 'Fail get Payment';

                    if ($regipaym === 'BPJS') {
                        $paymtype = '<span class="pay-bpjs">BPJS</span>';
                    } else {
                        $paymtype = '<span class="pay-umum">Umuum</span>';
                    }

                    $sudah_periksa = (int) $k['SUDAH_PERIKSA'];
                    $registat = $k['TRXA_REGI_STAT'];

                    // Logika Status
                    if ($registat === 'W') {
                        if ($sudah_periksa > 0) {
                            $badge = '<span class="status-badge status-done">Siap Diperiksa</span>';
                        } else {
                            $badge = '<span class="status-badge status-wait">Menunggu Skrining</span>';
                        }
                    } else {
                        $badge = '<span>Bukan Antrian</span>';
                    }

                    // Output Baris (Telah disesuaikan urutannya dengan tag <th>)
                    echo '<tr>';
                    echo '<td>' . $tgl_daftar . '<br>' . $waktu_daftar . '</td>';
                    echo '<td>' . $no_antrian . '</td>';
                    echo '<td>' . $poli_name . '</td>';
                    echo '<td>' . $pati_name . '</td>';
                    echo '<td>' . $paymtype . '</td>';
                    echo '<td>' . $badge . '</td>';
                    echo '<td>';
                    echo '<div class="action-group">';
                    echo '<button type="button" class="button-view" onclick="viewcode(\'' . $regicode . '\');">Periksa</button>';
                    echo '</div>';
                    echo '</td>';
                    echo '</tr>';
                }

                // Jika data tidak ditemukan
                if ($stmt->rowCount() == 0) {
                    echo '<tr><td colspan="7" style="text-align:center;">Data antrian tidak ditemukan.</td></tr>';
                }

            } catch (PDOException $e) {
                // Tampilkan pesan error jika query gagal (Jangan gunakan die() di production)
                echo '<tr><td colspan="7">Terjadi kesalahan pada sistem: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>