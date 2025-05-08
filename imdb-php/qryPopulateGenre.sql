INSERT INTO title_genre (title_id, genre_id)
SELECT t.tconst, g.genre_id
FROM title_basics_trim AS t
JOIN genres AS g ON ',' || t.genres || ',' LIKE '%,' || g.genre_name || ',%'
