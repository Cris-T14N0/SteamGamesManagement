-- ===========================================
--  CRIAÇÃO DA BASE DE DADOS
-- ===========================================
CREATE DATABASE IF NOT EXISTS sgm_db;
USE sgm_db;

-- ===========================================
--  TABELA USER
-- ===========================================
CREATE TABLE USER (
    id_user     INT AUTO_INCREMENT PRIMARY KEY,
    username    VARCHAR(50) NOT NULL,
    email       VARCHAR(255) NOT NULL,
    password    VARCHAR(255) NOT NULL,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uq_user_email (email),
    UNIQUE KEY uq_user_username (username)
);

-- ===========================================
--  TABELA LIBRARY
-- ===========================================
CREATE TABLE LIBRARY (
    id_library INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT NOT NULL,
    name       VARCHAR(100),

    CONSTRAINT fk_library_user
        FOREIGN KEY (user_id) REFERENCES USER(id_user)
        ON DELETE CASCADE
);

-- ===========================================
--  TABELA DEVELOPER, PUBLISHER E GAME
-- ===========================================
CREATE TABLE DEVELOPER (
    id_developer INT AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(255)
);

CREATE TABLE PUBLISHER (
    id_publisher INT AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(255)
);

CREATE TABLE GAME (
    id_game              INT AUTO_INCREMENT PRIMARY KEY,
    game_identifier      INT NOT NULL,

    title                VARCHAR(255) NOT NULL,
    release_date         DATE,
    age_rating           VARCHAR(20),
    about_description    TEXT,

    original_price       DECIMAL(10,2),
    discount_percentage  DECIMAL(5,2),
    discount_price       DECIMAL(10,2),

    overall_review       VARCHAR(50),
    overall_review_pct   DECIMAL(5,2),
    overall_review_count INT,

    recent_review        VARCHAR(50),
    recent_review_pct    DECIMAL(5,2),
    recent_review_count  INT,

    dlc_available        TINYINT(1),
    awards               TEXT,

    developer_id         INT,
    publisher_id         INT,

    UNIQUE KEY uq_game_identifier (game_identifier),

    CONSTRAINT fk_game_developer
        FOREIGN KEY (developer_id) REFERENCES DEVELOPER(id_developer),
    CONSTRAINT fk_game_publisher
        FOREIGN KEY (publisher_id) REFERENCES PUBLISHER(id_publisher)
);

-- ===========================================
--  TABELA LIBRARY_GAME (tabela de N:N)
-- ===========================================
CREATE TABLE LIBRARY_GAME (
    library_id INT NOT NULL,
    game_id    INT NOT NULL,

    PRIMARY KEY (library_id, game_id),

    CONSTRAINT fk_librarygame_library
        FOREIGN KEY (library_id) REFERENCES LIBRARY(id_library)
        ON DELETE CASCADE,

    CONSTRAINT fk_librarygame_game
        FOREIGN KEY (game_id) REFERENCES GAME(id_game)
        ON DELETE CASCADE
);

-- ===========================================
--  CATEGORY, GENRE E PLATFORM
-- ===========================================
CREATE TABLE CATEGORY (
    id_category INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(255)
);

CREATE TABLE GENRE (
    id_genre INT AUTO_INCREMENT PRIMARY KEY,
    name     VARCHAR(255)
);

CREATE TABLE PLATFORM (
    id_plataform INT AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(255)
);

-- ===========================================
--  TABELAS DE JUNÇÃO DE GAME
-- ===========================================

-- GAMECATEGORY
CREATE TABLE GAMECATEGORY (
    game_id     INT NOT NULL,
    category_id INT NOT NULL,

    PRIMARY KEY (game_id, category_id),

    CONSTRAINT fk_gamecategory_game
        FOREIGN KEY (game_id) REFERENCES GAME(id_game)
        ON DELETE CASCADE,

    CONSTRAINT fk_gamecategory_category
        FOREIGN KEY (category_id) REFERENCES CATEGORY(id_category)
        ON DELETE CASCADE
);

-- GAMEGENRE
CREATE TABLE GAMEGENRE (
    game_id INT NOT NULL,
    genre_id INT NOT NULL,

    PRIMARY KEY (game_id, genre_id),

    CONSTRAINT fk_gamegenre_game
        FOREIGN KEY (game_id) REFERENCES GAME(id_game)
        ON DELETE CASCADE,

    CONSTRAINT fk_gamegenre_genre
        FOREIGN KEY (genre_id) REFERENCES GENRE(id_genre)
        ON DELETE CASCADE
);

-- GAMEPLATFORM
CREATE TABLE GAMEPLATFORM (
    game_id     INT NOT NULL,
    platform_id INT NOT NULL,

    PRIMARY KEY (game_id, platform_id),

    CONSTRAINT fk_gameplatform_game
        FOREIGN KEY (game_id) REFERENCES GAME(id_game)
        ON DELETE CASCADE,

    CONSTRAINT fk_gameplatform_platform
        FOREIGN KEY (platform_id) REFERENCES PLATFORM(id_plataform)
        ON DELETE CASCADE
);
