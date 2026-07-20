-- Cover lookup/grouping kunjungan terakhir per pasien pada MEDIRECO05.
ALTER TABLE trxaregi
  ADD INDEX idx_rm05_patient_visit (TRXA_PATI_CODE, TRXA_VIEW_STAT, TRXA_REGI_DATE);
