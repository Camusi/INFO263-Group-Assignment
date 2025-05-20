INSERT INTO profession (name)
SELECT DISTINCT TRIM(value) FROM (
    SELECT substr(primaryProfession, 1, instr(primaryProfession || ',', ',') - 1) AS value FROM name_basics_trim
    UNION ALL
    SELECT substr(primaryProfession, instr(primaryProfession, ',') + 1) AS value FROM name_basics_trim
);