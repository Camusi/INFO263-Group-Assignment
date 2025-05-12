UPDATE title_basics_trim
SET genres = NULL
WHERE
    genres = '\N' OR genres = '\n';

UPDATE title_principals_trim
SET job = NULL
WHERE
    job = 'NULL' OR job = '\N' OR job = '\n';

UPDATE title_principals_trim
SET characters = NULL
WHERE
    characters = 'NULL' OR characters = '\N' OR characters = '\n';
