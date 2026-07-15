<!doctype html>
<?php include "conf/config.php";
//memulai session
session_start();

//cek adanya session
if (ISSET($_SESSION['username']))
{
	$user = $_SESSION['username'];

?>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Sistem Informasi Klinik Pratama">
<title>Group Account</title>
<link rel="shortcut icon" href="assets/img/icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/layouts/header.css">
    <link rel="stylesheet" href="assets/css/bootstrap/bootstrap.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/pure/pure-min.css">
<!--[if lte IE 8]>
	<link rel="stylesheet" href="assets/css/layouts/side-menu-old-ie.css">
<![endif]-->
<!--[if gt IE 8]><!-->
	<link rel="stylesheet" href="assets/css/layouts/side-menu.css">
<!--<![endif]-->
<style type="text/css">


        .button-update {
            background: rgb(66, 184, 221);
            /* this is a light blue */
        }

</style>

<style type="text/css">
	div.footerdate {
   position: fixed;
   left: 50;
   bottom: 50px;
   width: 90%;
   color: black;
   text-align: right;
}
div.footertime {
   position: fixed;
   left: 50;
   bottom: 20px;
   width: 90%;
   color: black;
   text-align: right;
}
</style>
</head>
<script type="text/javascript" src="js/jquery.js"></script>
<script>
$(document).ready(function() 
{
    setInterval(timestamp, 1000);
});  
    function timestamp() { $.ajax({ url: 'inc/timestamp.php', success: function(data) { $('#timestamp').html(data); }, }); }
</script>

<body onLoad="periksaakses('PASS_TBLA_ENTR');
">
<div id="wrapper">
	<!-- Side Menu -->
      <?php include "inc/side-menu.php"; ?>
	
	<!-- tampilan menu -->
	<div id="content-wrapper">
<?php include "inc/header.php"; ?>

		<div class="content">

        <!-- Tab Menu -->
          <div class="pure-menu pure-menu-horizontal">
            <ul class="pure-menu-list">

              <li class="pure-menu-item pure-menu-selected" onclick="javascript: location.href = 'TBLACOAC01.php'">
                <a class="pure-menu-link">
                Buat Group
                </a>
              </li>

              <li class="pure-menu-item pure-menu-disabled">
                Ubah Group
              </li>

              <li class="pure-menu-item pure-menu-selected" onclick="javascript: location.href = 'TBLACOAC03.php'">
                <a class="pure-menu-link">
                Hapus Group
                </a>
              </li>

              <li class="pure-menu-item pure-menu-selected" onclick="javascript: location.href = 'TBLACOAC04.php'">
                <a class="pure-menu-link">
                Lihat Group
                </a>
              </li>


            </ul>
          </div>
    <!-- Tab Menu -->

    <!-- Form Input -->
    <form name="frmtblacoac" class="pure-form pure-form-aligned" method="post" action="TBLACOAC02U.php">
      <fieldset>
        <div class="pure-control-group">

          <label for="optparentcode">Account Group :</label>
          <select name="optparentcode" id="optparentcode">
            <option value="1.">Aktiva</option>
            <option value="2.">Utang/Liabilities</option>
            <option value="3.">Ekuitas/Equity</option>
            <option value="4.">Pendapatan/Revenue</option>
            <option value="5.">Biaya/Expense</option>
          </select>

        </div><!-- pure-control-group -->

        <div class="pure-control-group">

        <label for="txtcoaccode">Account Code :</label>
              <input type="text" 
              name="txtcoaccode" 
              id="txtcoaccode" 
              maxlength ="2"
              placeholder="xx" 
              style="width: 40px;"
            onkeyup="var start = this.selectionStart;
                     var end = this.selectionEnd;
                     this.value = this.value.toUpperCase();
                     this.setSelectionRange(start, end);
                     if (value.length > 0) 
                        {
                            periksacoac(document.getElementById('optparentcode').value,this.value);
                        }
                    else
                        {
                            document.getElementById('labelmessage').style.visibility = 'hidden';
                            document.getElementById('labelmessage').innerHTML = '';
                        }"
                onkeydown="if (event.keyCode == 13 && value.length > 0) document.getElementById('txtcoacname').focus()">

                <input type="hidden"
                name="hidcoaccode"
                id="hidcoaccode">

            <span id="labelmessage" 
            class="pure-form-message" 
            style="visibility: hidden;">Code Account Not Found!.</span>

        </div><!-- pure-control-group -->

        <div class="pure-control-group">

        <label for="txtcoacname">Nama Akun :</label>
              <input type="text" 
              name="txtcoacname" 
              placeholder="Keterangan Nama Akun Induk" 
              id="txtcoacname" 
              maxlength ="100" 
              style="width: 800px;"
            onkeydown="if (event.keyCode == 13 && value.length > 0 )
                        {
                          if (document.getElementById('txtcoacname').value.length > 0)
                              {
                              document.frmtblacoac.submit();  
                              }
                          else              
                              {
                              document.getElementById('labelmessage2').style.visibility = '';
                              document.getElementById('txtcoacname').focus();
                              }
                         }
            ">
         <span id="labelmessage2" 
            class="pure-form-message" 
            style="visibility: hidden;">Fill Code Account!.</span>

      </div><!-- pure-control-group -->

      </fieldset>

      <fieldset>
          <a class="pure-button button-update" onclick="javascript:
              if (document.getElementById('txtcoaccode').value.length > 0)
              {
                document.frmtblacoac.submit();  
              }
              else              
              {
                document.getElementById('labelmessage2').style.visibility = '';
                document.getElementById('txtcoaccode').focus();
              }
        ">Submit</a>
        </fieldset>

    </form>

		</div><!-- div content -->
<div class="footerdate">
  	<span class="labelTime Time"><b>Date  :</b> <?php $tgl=date('d-m-Y'); echo $tgl;?></span>
</div>
<div class="footertime">
	<span class = "labelTime Time" id="timestamp"></span>
</div>


    	</div>
</div>
<script src="js/TBLACOAC02.js"></script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/ui.js"></script>


</body>
</html>
<?php
}
else

{
  header("Location: "."signin.php");
}
?>
