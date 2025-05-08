CREATE TABLE title_genre (
                             title_id TEXT NOT NULL,
                             genre_id INT NOT NULL,
                             PRIMARY KEY (title_id, genre_id),
                             FOREIGN KEY (title_id) REFERENCES title_basics_trim (tconst),
                             FOREIGN KEY (genre_id) REFERENCES genres (genre_id)
);