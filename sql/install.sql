CREATE TABLE exam_calendar_settings (
  setting varchar(20) NOT NULL,
  value   varchar(20) NOT NULL
);

-- Standardauswahl: Termin-Typ 3 (Klausur)
INSERT INTO exam_calendar_settings (
  setting, value
) VALUES (
  'exam_types', '4'
);

CREATE TABLE exam_calendar_faculty_colors (
  fakultaets_id varchar(32) NOT NULL,
  color         varchar(6)  NOT NULL,
  PRIMARY KEY (fakultaets_id)
);
