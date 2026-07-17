-- =============================================================================
-- Phase 1: Database Setup - TRXALABO05 Dynamic Lab Result
-- Source: plan/labo05.xlsx + plan/plan-trxalabo05.md
-- Naming: mengikuti konvensi siskadb (latin1, audit ENTR/UPDT, VIEW_STAT)
-- =============================================================================

-- -----------------------------------------------------------------------------
-- 1) MASTER TEMPLATE HEADER
--    Satu baris = 1 jenis layanan pemeriksaan (ex: DARAH RUTIN)
--    TEMP_MEDI_CODE di-link ke tblfmedi.TBLF_MEDI_CODE / trxatret.TRXA_MEDI_CODE
--    (isi mapping medi code sesuai data real di tblfmedi)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mst_lab_template` (
  `TEMP_CODE`       char(8)      NOT NULL COMMENT 'PK template, ex: TPL-0001',
  `TEMP_TIPE`       varchar(50)  DEFAULT NULL COMMENT 'Kategori, ex: HEMATOLOGI',
  `TEMP_NAME`       varchar(100) NOT NULL COMMENT 'Nama layanan, ex: DARAH RUTIN',
  `TEMP_MEDI_CODE`  char(8)      DEFAULT NULL COMMENT 'FK soft ke tblfmedi.TBLF_MEDI_CODE',
  `TEMP_NOTE`       varchar(200) DEFAULT NULL,
  `TEMP_VIEW_STAT`  char(1)      DEFAULT 'Y',
  `TEMP_ENTR_DATE`  date         DEFAULT NULL,
  `TEMP_ENTR_TIME`  time         DEFAULT NULL,
  `TEMP_ENTR_USER`  varchar(5)   DEFAULT NULL,
  `TEMP_UPDT_DATE`  date         DEFAULT NULL,
  `TEMP_UPDT_TIME`  time         DEFAULT NULL,
  `TEMP_UPDT_USER`  varchar(5)   DEFAULT NULL,
  PRIMARY KEY (`TEMP_CODE`),
  KEY `idx_temp_medi` (`TEMP_MEDI_CODE`),
  KEY `idx_temp_name` (`TEMP_NAME`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- -----------------------------------------------------------------------------
-- 2) MASTER TEMPLATE DETAIL
--    Item default per template (Item, Satuan, Nilai Rujukan, urutan)
--    ITEM_IS_HEADER='Y' = baris group label saja (ex: HITUNG JENIS LEUKOSIT)
--    ITEM_RUJUKAN disimpan full-text sesuai excel (multi gender/usia)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mst_lab_template_detail` (
  `DTL_ID`          int unsigned NOT NULL AUTO_INCREMENT,
  `TEMP_CODE`       char(8)      NOT NULL COMMENT 'FK ke mst_lab_template.TEMP_CODE',
  `ITEM_NAME`       varchar(100) NOT NULL COMMENT 'ex: HEMOGLOBIN, HEMATOKRIT',
  `ITEM_SATUAN`     varchar(30)  DEFAULT NULL COMMENT 'ex: gr/dL, %, per uL',
  `ITEM_RUJUKAN`    text         DEFAULT NULL COMMENT 'Nilai normal full text (multi baris OK)',
  `ITEM_USIA`       varchar(50)  DEFAULT NULL COMMENT 'Catatan usia default dari excel',
  `ITEM_URUT`       smallint     DEFAULT 0 COMMENT 'Urutan tampil di form',
  `ITEM_IS_HEADER`  char(1)      DEFAULT 'N' COMMENT 'Y=label group, bukan input hasil',
  `ITEM_VIEW_STAT`  char(1)      DEFAULT 'Y',
  `ITEM_ENTR_DATE`  date         DEFAULT NULL,
  `ITEM_ENTR_TIME`  time         DEFAULT NULL,
  `ITEM_ENTR_USER`  varchar(5)   DEFAULT NULL,
  `ITEM_UPDT_DATE`  date         DEFAULT NULL,
  `ITEM_UPDT_TIME`  time         DEFAULT NULL,
  `ITEM_UPDT_USER`  varchar(5)   DEFAULT NULL,
  PRIMARY KEY (`DTL_ID`),
  KEY `idx_dtl_temp` (`TEMP_CODE`, `ITEM_URUT`),
  CONSTRAINT `fk_dtl_temp`
    FOREIGN KEY (`TEMP_CODE`) REFERENCES `mst_lab_template` (`TEMP_CODE`)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- -----------------------------------------------------------------------------
-- 3) TRANSAKSI HASIL DETAIL (multiple row per registrasi + medi)
--    Menyimpan hasil input dinamis user (bisa beda dari template setelah add/remove)
--    Relasi:
--      TRXA_LABO_REGI  -> nomor daftar (trxalabo.TRXA_LABO_REGI / trxaregi)
--      TRXA_MEDI_CODE  -> jenis pemeriksaan dari trxatret
--      TEMP_CODE       -> template yang dipakai (opsional, untuk audit)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `trxalabo_detail_hasil` (
  `HASIL_ID`        int unsigned NOT NULL AUTO_INCREMENT,
  `TRXA_LABO_REGI`  char(14)     NOT NULL COMMENT 'Nomor daftar / registrasi lab',
  `TRXA_MEDI_CODE`  char(8)      DEFAULT NULL COMMENT 'Kode pemeriksaan (trxatret.TRXA_MEDI_CODE)',
  `TEMP_CODE`       char(8)      DEFAULT NULL COMMENT 'Template sumber (jika dari master)',
  `DTL_ID`          int unsigned DEFAULT NULL COMMENT 'Sumber item template (null jika manual)',
  `ITEM_NAME`       varchar(100) NOT NULL,
  `ITEM_HASIL`      varchar(50)  DEFAULT NULL COMMENT 'Nilai hasil input user',
  `ITEM_RUJUKAN`    text         DEFAULT NULL COMMENT 'Snapshot rujukan saat input',
  `ITEM_SATUAN`     varchar(30)  DEFAULT NULL COMMENT 'Snapshot satuan saat input',
  `ITEM_URUT`       smallint     DEFAULT 0,
  `ITEM_NOTE`       varchar(200) DEFAULT NULL,
  `HASIL_VIEW_STAT` char(1)      DEFAULT 'Y',
  `HASIL_ENTR_DATE` date         DEFAULT NULL,
  `HASIL_ENTR_TIME` time         DEFAULT NULL,
  `HASIL_ENTR_USER` varchar(5)   DEFAULT NULL,
  `HASIL_UPDT_DATE` date         DEFAULT NULL,
  `HASIL_UPDT_TIME` time         DEFAULT NULL,
  `HASIL_UPDT_USER` varchar(5)   DEFAULT NULL,
  PRIMARY KEY (`HASIL_ID`),
  KEY `idx_hasil_regi` (`TRXA_LABO_REGI`, `TRXA_MEDI_CODE`),
  KEY `idx_hasil_temp` (`TEMP_CODE`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- =============================================================================
-- SEEDER dari labo05.xlsx (Sheet1)
-- TEMP_MEDI_CODE sengaja NULL dulu — mapping ke tblfmedi di-update manual
--   UPDATE mst_lab_template SET TEMP_MEDI_CODE='TND-xxxx' WHERE TEMP_CODE='TPL-0001';
-- =============================================================================

INSERT INTO `mst_lab_template`
  (`TEMP_CODE`,`TEMP_TIPE`,`TEMP_NAME`,`TEMP_MEDI_CODE`,`TEMP_VIEW_STAT`,
   `TEMP_ENTR_DATE`,`TEMP_ENTR_TIME`,`TEMP_ENTR_USER`,
   `TEMP_UPDT_DATE`,`TEMP_UPDT_TIME`,`TEMP_UPDT_USER`)
VALUES
  ('TPL-0001','HEMATOLOGI','DARAH RUTIN',   NULL,'Y', CURDATE(), CURTIME(), 'SYS', CURDATE(), CURTIME(), 'SYS'),
  ('TPL-0002','HEMATOLOGI','DARAH LENGKAP', NULL,'Y', CURDATE(), CURTIME(), 'SYS', CURDATE(), CURTIME(), 'SYS'),
  ('TPL-0003','HEMATOLOGI','GOLONGAN DARAH',NULL,'Y', CURDATE(), CURTIME(), 'SYS', CURDATE(), CURTIME(), 'SYS'),
  ('TPL-0004','HEMATOLOGI','MASA PENDARAHAN (BT)',NULL,'Y', CURDATE(), CURTIME(), 'SYS', CURDATE(), CURTIME(), 'SYS'),
  ('TPL-0005','HEMATOLOGI','MASA PEMBEKUAN (CT)', NULL,'Y', CURDATE(), CURTIME(), 'SYS', CURDATE(), CURTIME(), 'SYS');

-- ---- TPL-0001 : DARAH RUTIN ----
INSERT INTO `mst_lab_template_detail`
  (`TEMP_CODE`,`ITEM_NAME`,`ITEM_SATUAN`,`ITEM_RUJUKAN`,`ITEM_USIA`,`ITEM_URUT`,`ITEM_IS_HEADER`,`ITEM_VIEW_STAT`,
   `ITEM_ENTR_DATE`,`ITEM_ENTR_TIME`,`ITEM_ENTR_USER`,`ITEM_UPDT_DATE`,`ITEM_UPDT_TIME`,`ITEM_UPDT_USER`)
VALUES
  ('TPL-0001','HEMOGLOBIN','gr/dL',
   'LAKI-LAKI : 13,0 - 18,0\nPEREMPUAN : 12,0 - 16,0\nANAK-ANAK : 11,0 - 16,0',
   '18 TAHUN - LANJUT USIA / < 18 TAHUN',1,'N','Y', CURDATE(),CURTIME(),'SYS',CURDATE(),CURTIME(),'SYS'),
  ('TPL-0001','HEMATOKRIT','%',
   'LAKI-LAKI : 38 - 55\nPEREMPUAN : 36 - 50\nANAK-ANAK : 33 - 48',
   '18 TAHUN - LANJUT USIA / < 18 TAHUN',2,'N','Y', CURDATE(),CURTIME(),'SYS',CURDATE(),CURTIME(),'SYS'),
  ('TPL-0001','HITUNG JUMLAH LEUKOSIT','per uL',
   '4000 - 10000','SEMUA USIA',3,'N','Y', CURDATE(),CURTIME(),'SYS',CURDATE(),CURTIME(),'SYS'),
  ('TPL-0001','HITUNG JUMLAH TROMBOSIT','per uL',
   '150000 - 450000','SEMUA USIA',4,'N','Y', CURDATE(),CURTIME(),'SYS',CURDATE(),CURTIME(),'SYS');

-- ---- TPL-0002 : DARAH LENGKAP ----
INSERT INTO `mst_lab_template_detail`
  (`TEMP_CODE`,`ITEM_NAME`,`ITEM_SATUAN`,`ITEM_RUJUKAN`,`ITEM_USIA`,`ITEM_URUT`,`ITEM_IS_HEADER`,`ITEM_VIEW_STAT`,
   `ITEM_ENTR_DATE`,`ITEM_ENTR_TIME`,`ITEM_ENTR_USER`,`ITEM_UPDT_DATE`,`ITEM_UPDT_TIME`,`ITEM_UPDT_USER`)
VALUES
  ('TPL-0002','HEMOGLOBIN','gr/dL',
   'LAKI-LAKI : 13,0 - 18,0\nPEREMPUAN : 12,0 - 16,0\nANAK-ANAK : 11,0 - 16,0',
   '18 TAHUN - LANJUT USIA / < 18 TAHUN',1,'N','Y', CURDATE(),CURTIME(),'SYS',CURDATE(),CURTIME(),'SYS'),
  ('TPL-0002','HEMATOKRIT','%',
   'LAKI-LAKI : 38 - 55\nPEREMPUAN : 36 - 50\nANAK-ANAK : 33 - 48',
   '18 TAHUN - LANJUT USIA / < 18 TAHUN',2,'N','Y', CURDATE(),CURTIME(),'SYS',CURDATE(),CURTIME(),'SYS'),
  ('TPL-0002','HITUNG JUMLAH LEUKOSIT','per uL',
   '4000 - 10000','SEMUA USIA',3,'N','Y', CURDATE(),CURTIME(),'SYS',CURDATE(),CURTIME(),'SYS'),
  ('TPL-0002','HITUNG JUMLAH TROMBOSIT','per uL',
   '150000 - 450000','SEMUA USIA',4,'N','Y', CURDATE(),CURTIME(),'SYS',CURDATE(),CURTIME(),'SYS'),
  ('TPL-0002','HITUNG JUMLAH ERITROSIT','juta/uL',
   'LAKI-LAKI : 4,0 - 6,2\nPEREMPUAN : 4,0 - 5,0\nANAK-ANAK : 3,6 - 5,3',
   '18 TAHUN - LANJUT USIA / < 18 TAHUN',5,'N','Y', CURDATE(),CURTIME(),'SYS',CURDATE(),CURTIME(),'SYS'),
  ('TPL-0002','LED (LAJU ENDAP DARAH)','mm/jam',
   'LAKI-LAKI : 0 - 10\nPEREMPUAN : 0 - 15\nANAK-ANAK : 0 - 15',
   '18 TAHUN - LANJUT USIA / < 18 TAHUN',6,'N','Y', CURDATE(),CURTIME(),'SYS',CURDATE(),CURTIME(),'SYS'),
  ('TPL-0002','HITUNG JENIS LEUKOSIT',NULL,NULL,NULL,7,'Y','Y', CURDATE(),CURTIME(),'SYS',CURDATE(),CURTIME(),'SYS'),
  ('TPL-0002','BASOFIL','%','0 - 1','SEMUA USIA',8,'N','Y', CURDATE(),CURTIME(),'SYS',CURDATE(),CURTIME(),'SYS'),
  ('TPL-0002','EOSINOFIL','%','0 - 3','SEMUA USIA',9,'N','Y', CURDATE(),CURTIME(),'SYS',CURDATE(),CURTIME(),'SYS'),
  ('TPL-0002','NEUTROFIL BATANG','%','0 - 6','SEMUA USIA',10,'N','Y', CURDATE(),CURTIME(),'SYS',CURDATE(),CURTIME(),'SYS'),
  ('TPL-0002','NEUTROFIL SEGMENT','%','50 - 70','SEMUA USIA',11,'N','Y', CURDATE(),CURTIME(),'SYS',CURDATE(),CURTIME(),'SYS'),
  ('TPL-0002','LIMFOSIT','%','20 - 40','SEMUA USIA',12,'N','Y', CURDATE(),CURTIME(),'SYS',CURDATE(),CURTIME(),'SYS'),
  ('TPL-0002','MONOSIT','%','2 - 8','SEMUA USIA',13,'N','Y', CURDATE(),CURTIME(),'SYS',CURDATE(),CURTIME(),'SYS'),
  ('TPL-0002','MCV','fl',
   'LAKI-LAKI : 92 - 100\nPEREMPUAN : 92 - 100\nANAK-ANAK : 85 - 90',
   '18 TAHUN - LANJUT USIA / < 18 TAHUN',14,'N','Y', CURDATE(),CURTIME(),'SYS',CURDATE(),CURTIME(),'SYS'),
  ('TPL-0002','MCH','pg',
   'LAKI-LAKI : 28 - 31\nPEREMPUAN : 28 - 31\nANAK-ANAK : 25 - 30',
   '18 TAHUN - LANJUT USIA / < 18 TAHUN',15,'N','Y', CURDATE(),CURTIME(),'SYS',CURDATE(),CURTIME(),'SYS'),
  ('TPL-0002','MCHC','g/L',
   'LAKI-LAKI : 30 - 32\nPEREMPUAN : 30 - 32\nANAK-ANAK : 28 - 30',
   '18 TAHUN - LANJUT USIA / < 18 TAHUN',16,'N','Y', CURDATE(),CURTIME(),'SYS',CURDATE(),CURTIME(),'SYS');

-- ---- TPL-0003 : GOLONGAN DARAH (item bebas / manual input di form) ----
INSERT INTO `mst_lab_template_detail`
  (`TEMP_CODE`,`ITEM_NAME`,`ITEM_SATUAN`,`ITEM_RUJUKAN`,`ITEM_USIA`,`ITEM_URUT`,`ITEM_IS_HEADER`,`ITEM_VIEW_STAT`,
   `ITEM_ENTR_DATE`,`ITEM_ENTR_TIME`,`ITEM_ENTR_USER`,`ITEM_UPDT_DATE`,`ITEM_UPDT_TIME`,`ITEM_UPDT_USER`)
VALUES
  ('TPL-0003','GOLONGAN DARAH',NULL,NULL,NULL,1,'N','Y', CURDATE(),CURTIME(),'SYS',CURDATE(),CURTIME(),'SYS'),
  ('TPL-0003','RHESUS',NULL,NULL,NULL,2,'N','Y', CURDATE(),CURTIME(),'SYS',CURDATE(),CURTIME(),'SYS');

-- ---- TPL-0004 : MASA PENDARAHAN (BT) ----
INSERT INTO `mst_lab_template_detail`
  (`TEMP_CODE`,`ITEM_NAME`,`ITEM_SATUAN`,`ITEM_RUJUKAN`,`ITEM_USIA`,`ITEM_URUT`,`ITEM_IS_HEADER`,`ITEM_VIEW_STAT`,
   `ITEM_ENTR_DATE`,`ITEM_ENTR_TIME`,`ITEM_ENTR_USER`,`ITEM_UPDT_DATE`,`ITEM_UPDT_TIME`,`ITEM_UPDT_USER`)
VALUES
  ('TPL-0004','MASA PENDARAHAN (BT)','MENIT','1 - 3','SEMUA USIA',1,'N','Y',
   CURDATE(),CURTIME(),'SYS',CURDATE(),CURTIME(),'SYS');

-- ---- TPL-0005 : MASA PEMBEKUAN (CT) ----
INSERT INTO `mst_lab_template_detail`
  (`TEMP_CODE`,`ITEM_NAME`,`ITEM_SATUAN`,`ITEM_RUJUKAN`,`ITEM_USIA`,`ITEM_URUT`,`ITEM_IS_HEADER`,`ITEM_VIEW_STAT`,
   `ITEM_ENTR_DATE`,`ITEM_ENTR_TIME`,`ITEM_ENTR_USER`,`ITEM_UPDT_DATE`,`ITEM_UPDT_TIME`,`ITEM_UPDT_USER`)
VALUES
  ('TPL-0005','MASA PEMBEKUAN (CT)','MENIT','5 - 15','SEMUA USIA',1,'N','Y',
   CURDATE(),CURTIME(),'SYS',CURDATE(),CURTIME(),'SYS');

-- =============================================================================
-- HELPER: mapping medi code (jalankan setelah cek tblfmedi)
-- Contoh:
--   UPDATE mst_lab_template SET TEMP_MEDI_CODE='TND-0228' WHERE TEMP_CODE='TPL-0001';
--   UPDATE mst_lab_template SET TEMP_MEDI_CODE='TND-????' WHERE TEMP_CODE='TPL-0002';
-- =============================================================================

-- Query cek layanan lab di master tindakan:
-- SELECT TBLF_MEDI_CODE, TBLF_MEDI_NAME
-- FROM tblfmedi
-- WHERE TBLF_VIEW_STAT='Y'
--   AND (TBLF_MEDI_NAME LIKE '%DARAH%' OR TBLF_MEDI_NAME LIKE '%LAB%'
--     OR TBLF_MEDI_NAME LIKE '%BT%' OR TBLF_MEDI_NAME LIKE '%CT%'
--     OR TBLF_MEDI_NAME LIKE '%GOLONGAN%')
-- ORDER BY TBLF_MEDI_NAME;
