USE medithink_db;

CREATE TABLE IF NOT EXISTS pazul_game_records (
    game_record_id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    game_name VARCHAR(50) NOT NULL DEFAULT 'pazul',
    score INT NOT NULL,
    played_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (game_record_id),
    KEY idx_pazul_records_user_game (user_id, game_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS pazul_game_stats (
    stat_id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    game_name VARCHAR(50) NOT NULL DEFAULT 'pazul',
    best_score INT NOT NULL DEFAULT 0,
    play_count INT NOT NULL DEFAULT 0,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (stat_id),
    UNIQUE KEY uq_pazul_stats_user_game (user_id, game_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
