-- Cover indexes untuk query dashboard VIEWDATA.php.
-- Dashboard memfilter: (TRXA_VIEW_STAT, TRXA_ENTR_DATE) dan agregasi pada poli/paym.
-- Pemakaian di SQL penuh, tambahkan satu per satu (idempotent pakai ALTER; jika sudah ada, abaikan error 1061).

ALTER TABLE trxaregi
  ADD INDEX idx_view_date (TRXA_VIEW_STAT, TRXA_REGI_DATE, TRXA_REGI_POLI, TRXA_REGI_PAYM);

ALTER TABLE trxasale
  ADD INDEX idx_view_date (TRXA_VIEW_STAT, TRXA_ENTR_DATE);

ALTER TABLE trxadrug
  ADD INDEX idx_view_date (TRXA_VIEW_STAT, TRXA_DRUG_STAT, TRXA_ENTR_DATE);

ALTER TABLE trxaprsc
  ADD INDEX idx_view_date (TRXA_VIEW_STAT, TRXA_ENTR_DATE);

ALTER TABLE trxadiag
  ADD INDEX idx_view_date (TRXA_VIEW_STAT, TRXA_ENTR_DATE);
