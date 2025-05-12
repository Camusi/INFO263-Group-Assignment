-- Indexes for title_basics_trim
CREATE INDEX IF NOT EXISTS idx_title_tconst ON title_basics_trim(tconst);
CREATE INDEX IF NOT EXISTS idx_title_genres ON title_basics_trim(genres);
CREATE INDEX IF NOT EXISTS idx_primaryTitle ON title_basics_trim(primaryTitle);
CREATE INDEX IF NOT EXISTS idx_originalTitle ON title_basics_trim(originalTitle);

-- Indexes for title_ratings_trim
CREATE INDEX IF NOT EXISTS idx_ratings_tconst ON title_ratings_trim(tconst);

-- Indexes for name_basics_trim
CREATE INDEX IF NOT EXISTS idx_name_nconst ON name_basics_trim(nconst);
CREATE INDEX IF NOT EXISTS idx_primaryProfession ON name_basics_trim(primaryProfession);

-- Indexes for profession and name_profession
CREATE INDEX IF NOT EXISTS idx_profession_name ON profession(name);
CREATE INDEX IF NOT EXISTS idx_name_profession_ids ON name_profession(name_id, profession_id);
