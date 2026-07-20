-- Cover indexes untuk query kasir TRXASALE01C.php / TRXASALE01V-INVC.php.
-- Filter pada regi/trxap code, view_stat, status, dan join ke master medis.

ALTER TABLE trxatret
  ADD INDEX idx_tret_regi (TRXA_TRET_CODE, TRXA_VIEW_STAT, TRXA_TRET_STAT);

ALTER TABLE trxacsbl
  ADD INDEX idx_csbl_regi (TRXA_CSBL_CODE, TRXA_VIEW_STAT, TRXA_CSBL_STAT);

ALTER TABLE trxaprsc
  ADD INDEX idx_prsc_regi (TRXA_PRSC_CODE, TRXA_VIEW_STAT, TRXA_PRSC_STAT);

ALTER TABLE tblfmedi
  ADD INDEX idx_fmedi_type (TBLF_MEDI_TYPE, TBLF_MEDI_CODE);
