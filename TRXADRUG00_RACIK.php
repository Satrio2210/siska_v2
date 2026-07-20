<?php
session_start();
include "conf/config.php";
include "inc/sanie.php";

if (!isset($_SESSION['username'])) {
    die("Unauthorized");
}

$action = $_GET['action'] ?? '';

// ==========================================
// ACTION: ADD HEAD
// ==========================================
if ($action == 'add_head') {
    $prsccode = $_POST['prsccode'] ?? '';
    $nama = $_POST['nama'] ?? '';
    $signa = $_POST['signa'] ?? '';
    $qty = (int) ($_POST['qty'] ?? 1);

    if ($prsccode == '' || $nama == '') {
        die("ERROR|Data tidak lengkap");
    }

    // Cari usage dari signa jika diinput nama signa-nya
    $usag = '';
    $qsg = $db->prepare("SELECT TBLP_SGNA_USAG FROM tblpsgna WHERE TBLP_SGNA_NAME = :signa1 OR TBLP_SGNA_CODE = :signa2 LIMIT 1");
    $qsg->execute([
        ':signa1' => $signa,
        ':signa2' => $signa
    ]);
    if ($row = $qsg->fetch(PDO::FETCH_ASSOC)) {
        $usag = $row['TBLP_SGNA_USAG'];
    }

    $sql = "INSERT INTO trxaracik_head (TRXAR_CODE, TRXAR_NAMA, TRXAR_SGNA, TRXAR_USAG, TRXAR_QTY, TRXAR_VIEW_STAT, TRXAR_ENTR_DATE, TRXAR_ENTR_TIME, TRXAR_ENTR_USER)
            VALUES (:code, :nama, :signa, :usag, :qty, 'Y', CURDATE(), CURTIME(), :user)";
    $q = $db->prepare($sql);
    $q->execute([
        ':code' => $prsccode,
        ':nama' => $nama,
        ':signa' => $signa,
        ':usag' => $usag,
        ':qty' => $qty,
        ':user' => $_SESSION['username']
    ]);
    $new_id = $db->lastInsertId();
    echo "OK|" . $new_id;
    exit;
}

// ==========================================
// ACTION: DEL HEAD
// ==========================================
if ($action == 'del_head') {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) {
        die("ERROR|ID tidak valid");
    }

    $db->beginTransaction();
    // Soft delete head
    $q1 = $db->prepare("UPDATE trxaracik_head SET TRXAR_VIEW_STAT = 'N' WHERE TRXAR_ID = :id");
    $q1->execute([':id' => $id]);

    // Soft delete items associated with this racikan
    $q2 = $db->prepare("UPDATE trxaprsc SET TRXA_VIEW_STAT = 'N' WHERE TRXA_RACIK_ID = :id");
    $q2->execute([':id' => $id]);
    $db->commit();

    echo "OK";
    exit;
}

// ==========================================
// ACTION: TEMPLATE HEAD (Tampil Tabel Racikan)
// ==========================================
if ($action == 'tmpl_head') {
    $prsccode = $_POST['prsccode'] ?? '';

    $sql = "SELECT * FROM trxaracik_head WHERE TRXAR_CODE = :code AND TRXAR_VIEW_STAT = 'Y' ORDER BY TRXAR_ID DESC";
    $q = $db->prepare($sql);
    $q->execute([':code' => $prsccode]);

    $count = 0;
    ?> <!-- Mulai HTML murni untuk tabel head -->

    <style>
        .table-container {
            overflow: auto;
            border-radius: 16px;
            max-height: 420px;
            border: 1px solid #e2e8f0;
            margin-top: 10px;
        }

        .tbl-racik {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .tbl-racik th,
        .tbl-racik td {
            padding: 10px 20px;
        }

        .tbl-racik thead {
            background: #0D9488;
            color: white;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .tbl-racik th {
            font-size: 13px;
            font-weight: 700;
            text-align: center;
            vertical-align: middle;
            border: none;
            letter-spacing: 0.3px;
        }

        .tbl-racik td {
            font-size: 12px;
            /* color: #374151; */
            text-align: center;
            vertical-align: middle;
            border-bottom: 1px solid #e5e7eb;
            overflow-wrap: break-word;
            word-break: break-word;
        }

        .tbl-racik th:nth-child(1),
        .tbl-racik td:nth-child(1) {
            width: 25%;
        }

        .tbl-racik th:nth-child(2),
        .tbl-racik td:nth-child(2) {
            width: 35%;
        }

        .tbl-racik th:nth-child(3),
        .tbl-racik td:nth-child(3) {
            width: 10%;
        }

        .tbl-racik th:nth-child(4),
        .tbl-racik td:nth-child(4) {
            width: 30%;
        }
    </style>

    <div class="table-container">
        <table class="tbl-racik">
            <thead>
                <tr>
                    <th>Nama Racikan</th>
                    <th>Signa</th>
                    <th>Qty</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

                <?php while ($row = $q->fetch(PDO::FETCH_ASSOC)):
                    $count++; ?>
                    <tr id="row_racik_<?= htmlspecialchars($row['TRXAR_ID']) ?>"
                        onclick="selectRacikanRow(<?= htmlspecialchars($row['TRXAR_ID']) ?>, '<?= htmlspecialchars($row['TRXAR_NAMA'], ENT_QUOTES) ?>')">

                        <td><?= htmlspecialchars($row['TRXAR_NAMA']) ?></td>
                        <td><?= htmlspecialchars($row['TRXAR_SGNA']) ?></td>
                        <td><?= htmlspecialchars($row['TRXAR_QTY']) ?></td>

                        <td onclick="event.stopPropagation();">
                            <div class="action-button">
                                <button type="button" class="btn-icon btn-sel btn-fit"
                                    onclick="selectRacikanRow(<?= htmlspecialchars($row['TRXAR_ID']) ?>, '<?= htmlspecialchars($row['TRXAR_NAMA'], ENT_QUOTES) ?>')"><i
                                        class="bi bi-hand-index-thumb-fill"></i>
                                </button>
                                <button type="button" class="btn-icon btn-del btn-fit"
                                    onclick="hapusRacikan(<?= htmlspecialchars($row['TRXAR_ID']) ?>)"><i
                                        class="bi bi-trash-fill"></i>
                                </button>
                            </div>
                        </td>

                    </tr>
                <?php endwhile; ?>

                <?php if ($count == 0): ?>
                    <tr>
                        <td colspan="4" style="text-align: center; color: #64748b; font-style: italic;">
                            Belum ada racikan. Silakan buat dahulu.
                        </td>
                    </tr>
                <?php endif; ?>

            </tbody>
        </table>
    </div>

    <hr class="modern-divider">

    <?php
    exit;
}

// ==========================================
// ACTION: ADD DETAIL
// ==========================================
if ($action == 'add_detail') {
    $prsccode = $_POST['prsccode'] ?? '';
    $stockcode = $_POST['stockcode'] ?? '';
    $stockbtch = $_POST['stockbtch'] ?? '';
    $stockpric = $_POST['stockpric'] ?? 0;
    $stockqty = $_POST['stockqty'] ?? 1;
    $racik_id = (int) ($_POST['racik_id'] ?? 0);

    if ($prsccode == '' || $stockcode == '' || $racik_id <= 0) {
        die("ERROR|Data komposisi tidak lengkap");
    }

    // Ambil info signa & usage dari header racikan
    $qhead = $db->prepare("SELECT TRXAR_SGNA, TRXAR_USAG FROM trxaracik_head WHERE TRXAR_ID = :id LIMIT 1");
    $qhead->execute([':id' => $racik_id]);
    $head = $qhead->fetch(PDO::FETCH_ASSOC);
    if (!$head) {
        die("ERROR|Racikan tidak ditemukan");
    }

    $signa_name = $head['TRXAR_SGNA'];
    $usag = $head['TRXAR_USAG'];

    // Cari signa code dari namanya jika ada
    $signa_code = '01'; // default
    $qsg = $db->prepare("SELECT TBLP_SGNA_CODE FROM tblpsgna WHERE TBLP_SGNA_NAME = :name LIMIT 1");
    $qsg->execute([':name' => $signa_name]);
    if ($row = $qsg->fetch(PDO::FETCH_ASSOC)) {
        $signa_code = $row['TBLP_SGNA_CODE'];
    }

    // Info dokter dan poli dari registrasi
    $qreg = $db->prepare("SELECT TRXA_REGI_DOCT, TRXA_REGI_POLI FROM trxaregi WHERE TRXA_REGI_CODE = :code LIMIT 1");
    $qreg->execute([':code' => $prsccode]);
    $reg = $qreg->fetch(PDO::FETCH_ASSOC);
    $doctor = $reg['TRXA_REGI_DOCT'] ?? '';
    $poli = $reg['TRXA_REGI_POLI'] ?? '';

    $userid = $_SESSION['username'];

    // Cek apakah obat sudah ada di racikan ini
    $qcek = $db->prepare("SELECT COUNT(*) FROM trxaprsc WHERE TRXA_PRSC_CODE = :code AND TRXA_STOCK_CODE = :stock AND TRXA_RACIK_ID = :racik_id AND TRXA_VIEW_STAT = 'Y'");
    $qcek->execute([':code' => $prsccode, ':stock' => $stockcode, ':racik_id' => $racik_id]);
    $exists = $qcek->fetchColumn();

    if ($exists == 0) {
        $sql = "INSERT INTO trxaprsc (
                    TRXA_PRSC_CODE, TRXA_PRSC_DOCT, TRXA_STOCK_CODE, TRXA_STOCK_BTCH, TRXA_PRSC_CONC,
                    TRXA_STOCK_PRIC, TRXA_STOCK_QUTY, TRXA_PRSC_SGNA, TRXA_PRSC_USAG, TRXA_MEDI_ROOM,
                    TRXA_PRSC_STAT, TRXA_VIEW_STAT, TRXA_RACIK_ID,
                    TRXA_ENTR_DATE, TRXA_ENTR_TIME, TRXA_ENTR_USER,
                    TRXA_UPDT_DATE, TRXA_UPDT_TIME, TRXA_UPDT_USER
                ) VALUES (
                    :code, :doctor, :stock, :btch, 'Y',
                    :pric, :qty, :sgna, :usag, :poli,
                    'A', 'Y', :racik_id,
                    CURDATE(), CURTIME(), :user,
                    CURDATE(), CURTIME(), :user
                )";
        $q = $db->prepare($sql);
        $q->execute([
            ':code' => $prsccode,
            ':doctor' => $doctor,
            ':stock' => $stockcode,
            ':btch' => $stockbtch,
            ':pric' => $stockpric,
            ':qty' => $stockqty,
            ':sgna' => $signa_code,
            ':usag' => $usag,
            ':poli' => $poli,
            ':racik_id' => $racik_id,
            ':user' => $userid
        ]);
    } else {
        $sql = "UPDATE trxaprsc SET 
                    TRXA_STOCK_QUTY = TRXA_STOCK_QUTY + :qty,
                    TRXA_STOCK_BTCH = :btch,
                    TRXA_STOCK_PRIC = :pric,
                    TRXA_UPDT_DATE = CURDATE(),
                    TRXA_UPDT_TIME = CURTIME(),
                    TRXA_UPDT_USER = :user
                WHERE TRXA_PRSC_CODE = :code AND TRXA_STOCK_CODE = :stock AND TRXA_RACIK_ID = :racik_id AND TRXA_VIEW_STAT = 'Y'";
        $q = $db->prepare($sql);
        $q->execute([
            ':qty' => $stockqty,
            ':btch' => $stockbtch,
            ':pric' => $stockpric,
            ':user' => $userid,
            ':code' => $prsccode,
            ':stock' => $stockcode,
            ':racik_id' => $racik_id
        ]);
    }

    echo "OK";
    exit;
}

// ==========================================
// ACTION: DEL DETAIL
// ==========================================
if ($action == 'del_detail') {
    $prsccode = $_POST['prsccode'] ?? '';
    $stockcode = $_POST['stockcode'] ?? '';
    $racik_id = (int) ($_POST['racik_id'] ?? 0);

    if ($prsccode == '' || $stockcode == '' || $racik_id <= 0) {
        die("ERROR|Data hapus tidak lengkap");
    }

    $sql = "UPDATE trxaprsc SET TRXA_VIEW_STAT = 'N' WHERE TRXA_PRSC_CODE = :code AND TRXA_STOCK_CODE = :stock AND TRXA_RACIK_ID = :racik_id";
    $q = $db->prepare($sql);
    $q->execute([
        ':code' => $prsccode,
        ':stock' => $stockcode,
        ':racik_id' => $racik_id
    ]);

    echo "OK";
    exit;
}

// ==========================================
// ACTION: TEMPLATE DETAIL (Tampil Tabel Komposisi)
// ==========================================
if ($action == 'tmpl_detail') {
    $racik_id = (int) ($_POST['racik_id'] ?? 0);

    if ($racik_id <= 0) {
        echo '<p style="color: #64748b; font-style: italic;">Pilih racikan terlebih dahulu.</p>';
        exit;
    }

    $sql = "SELECT p.*, 
            (SELECT INVE_PART_NAME FROM invemast WHERE INVE_MAST_CODE = p.TRXA_STOCK_CODE) AS STOCK_NAME,
            (SELECT INVE_SALE_UNIT FROM invemast WHERE INVE_MAST_CODE = p.TRXA_STOCK_CODE) AS UNIT_CODE,
            (SELECT TBLI_UNIT_NAME FROM tbliunit WHERE TBLI_UNIT_CODE = UNIT_CODE) AS UNIT_NAME
            FROM trxaprsc p 
            WHERE p.TRXA_RACIK_ID = :racik_id AND p.TRXA_VIEW_STAT = 'Y' 
            ORDER BY p.TRXA_STOCK_CODE";
    $q = $db->prepare($sql);
    $q->execute([':racik_id' => $racik_id]);

    $count = 0;
    ?> <!-- Mulai HTML murni untuk tabel detail -->

    <style>
        .tbl-komposisi {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .tbl-komposisi th,
        .tbl-komposisi td {
            padding: 10px 20px;
        }

        .tbl-komposisi thead {
            background: #10b981;
            color: white;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .tbl-komposisi th {
            font-size: 13px;
            font-weight: 700;
            text-align: center;
            vertical-align: middle;
            border: none;
            letter-spacing: 0.3px;
        }

        .tbl-komposisi td {
            font-size: 12px;
            /* color: #374151; */
            text-align: center;
            vertical-align: middle;
            border-bottom: 1px solid #e5e7eb;
            overflow-wrap: break-word;
            word-break: break-word;
        }

        .tbl-komposisi th:nth-child(1),
        .tbl-komposisi td:nth-child(1) {
            width: 50%;
        }

        .tbl-komposisi th:nth-child(2),
        .tbl-komposisi td:nth-child(2) {
            width: 20%;
        }

        .tbl-komposisi th:nth-child(3),
        .tbl-komposisi td:nth-child(3) {
            width: 30%;
        }
    </style>

    <div class="table-container">
        <table class="tbl-komposisi">
            <thead>
                <tr>
                    <th>Nama Obat</th>
                    <th>Qty</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

                <?php while ($row = $q->fetch(PDO::FETCH_ASSOC)):
                    $count++; ?>
                    <tr>
                        <td><?= htmlspecialchars($row['STOCK_NAME']) ?></td>
                        <td><?= htmlspecialchars($row['TRXA_STOCK_QUTY']) ?>         <?= htmlspecialchars($row['UNIT_NAME']) ?></td>

                        <td>
                            <button type="button" class="btn-icon btn-del"
                                onclick="hapusObatKomposisi('<?= htmlspecialchars($row['TRXA_PRSC_CODE']) ?>', '<?= htmlspecialchars($row['TRXA_STOCK_CODE']) ?>')"><i
                                    class="bi bi-trash-fill"></i>
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>

                <?php if ($count == 0): ?>
                    <tr>
                        <td colspan="3" style="text-align: center; color: #64748b; font-style: italic;">
                            Belum ada obat dalam racikan ini. Silakan tambahkan.
                        </td>
                    </tr>
                <?php endif; ?>

            </tbody>
        </table>
    </div>

    <?php
    exit;
}
?>