<?php
include "conf/config.php";
?>

<style>
  .table-container {
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    margin-top: 10px;
    overflow: hidden;
  }

  #screendiaglist {
    width: 100%;
    border-collapse: collapse;
  }

  #screendiaglist tbody {
    display: block;
    max-height: 250px;
    overflow-y: auto;
  }

  #screendiaglist thead,
  #screendiaglist tbody tr {
    display: table;
    width: 100%;
    table-layout: fixed;
  }

  #screendiaglist thead {
    background: #10b981;
    color: white;
    /* position: sticky;
    top: 0;
    z-index: 10; */
    /* height: 30px; */
  }

  #screendiaglist th {
    padding: 10px 6px;
    font-size: 13px;
    font-weight: 700;
    text-align: center;
    vertical-align: middle;
    border: none;
    letter-spacing: 0.3px;
  }

  #screendiaglist td {
    padding: 8px 6px;
    font-size: 12px;
    /* color: #374151; */
    text-align: center;
    vertical-align: middle;
    border-bottom: 1px solid #e5e7eb;
    overflow-wrap: break-word;
    word-break: break-word;
  }

  #screendiaglist th:nth-child(1),
  #screendiaglist td:nth-child(1) {
    width: 30%;
  }

  #screendiaglist th:nth-child(2),
  #screendiaglist td:nth-child(2) {
    width: 70%;
  }

  #screen_suggestion tr {
    transition: .2s;
  }

  #screendiaglist tbody tr:hover {
    background: #d0e3f7;
  }
</style>

<div class="table-container">
  <table id="screendiaglist">
    <thead>
      <tr>
        <th>Kode</th>
        <th>Diagnosa</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $rawdata = $_POST['q'];
      list($regicode, $diagcode) = explode("|", $rawdata);

      $xquery = "SELECT 
            DIAG_ICD_CODE AS ICD_CODE, 
            DIAG_ICD_NOTE AS ICD_NAME
          FROM diagmast
          WHERE 
          (
              DIAG_ICD_CODE LIKE '$diagcode%'
              OR DIAG_ICD_NOTE LIKE '$diagcode%'
              OR DIAG_ICD_NOTE LIKE '%$diagcode%'
          )
          AND DIAG_VIEW_STAT = 'Y'
          ORDER BY DIAG_ICD_CODE";

      $q = $db->query($xquery) or die("Gagal ambil data !!");
      $found = false;
      while ($k = $q->fetch(PDO::FETCH_ASSOC)) {

        $found = true;

        $outicdcode = $k['ICD_CODE'];
        $outicdname = $k['ICD_NAME'];

        echo '<tr>';

        echo '<td onClick="isidiagnosa(\'' . $regicode . '\',\'' . $outicdcode . '\',\'' . $outicdname . '\');" 
      style="cursor:pointer">' . $outicdcode . '</td>';

        echo '<td onClick="isidiagnosa(\'' . $regicode . '\',\'' . $outicdcode . '\',\'' . $outicdname . '\');" 
      style="cursor:pointer">' . $outicdname . '</td>';

        echo '</tr>';

        if (!$found) {
          echo '<tr>
            <td colspan="2" style="
                text-align:center;
                padding:14px;
                color:#6b7280;
            ">
                Diagnosa tidak ditemukan
            </td>
          </tr>';
        }
      }
      ?>
    </tbody>
  </table>
</div>