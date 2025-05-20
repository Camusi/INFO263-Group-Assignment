# INFO263-Group-Assignment

The core task is to develop a website for showcasing and augmenting the IMDB titles. This task will require a bit of front-end work for designing HTML pages with forms and back-end functionality written in PHP code; and then, to give the website a proper look-and-feel you'll need to add some interactivity with JavaScript and CSS. There is a lot of freedom as to how you may approach this task. At the very minimum your solution must include these basic features/properties:

## 2025 Group 1 Solution

### Directories:
#### 2025-group1
- All our workload is here.
- Note that the imdb-php file has now been moved to 2025-group1/objects as is no longer needed

### Database tables:

* **genres** — List of all genres for a title extracted from `title_basics`
* **title_genre** — Combination table between `title_basics` and `genres`
* **professions** — List of all professions of a person extracted from `name_basics`
* **name_professions** — Combination table between `name_basics` and `professions`
* **title_known_for** — Combination table between `title_basics` and `name_basics`
* **title_principals_trim**
* **title_writer_trim**
* **title_ratings_trim**
* **title_director_trim**
* **title_basics_trim**
* **name_basics_trim**
* **known_for_titles_trim**

