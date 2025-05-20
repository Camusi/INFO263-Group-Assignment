CREATE TABLE name_profession (
    name_id TEXT NOT NULL,
    profession_id INT NOT NULL,
    PRIMARY KEY (name_id, profession_id),
    FOREIGN KEY (profession_id) REFERENCES profession(id)
);