<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

// Config is two levels up from api/library/
include '../../config.php';

$game_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];

if ($game_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid game ID']);
    exit;
}

// Get user's library ID
$stmt = $conn->prepare("SELECT id_library FROM LIBRARY WHERE user_id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$libResult = $stmt->get_result();
$library_id = 0;

if ($libResult->num_rows > 0) {
    $libRow = $libResult->fetch_assoc();
    $library_id = $libRow['id_library'];
}
$stmt->close();

// Fetch complete game details
$sql = "
    SELECT 
        GAME.id_game,
        GAME.title,
        GAME.game_identifier,
        GAME.release_date,
        GAME.age_rating,
        GAME.about_description,
        GAME.original_price,
        GAME.discount_percentage,
        GAME.discount_price,
        GAME.overall_review,
        GAME.overall_review_pct,
        GAME.overall_review_count,
        GAME.recent_review,
        GAME.recent_review_pct,
        GAME.recent_review_count,
        GAME.dlc_available,
        GAME.awards,
        DEVELOPER.name as developer,
        PUBLISHER.name as publisher,
        GROUP_CONCAT(DISTINCT GENRE.name ORDER BY GENRE.name SEPARATOR ', ') as genres,
        GROUP_CONCAT(DISTINCT CATEGORY.name ORDER BY CATEGORY.name SEPARATOR ', ') as categories,
        GROUP_CONCAT(DISTINCT PLATFORM.name ORDER BY PLATFORM.name SEPARATOR ', ') as platforms,
        (CASE WHEN LIBRARY_GAME.game_id IS NOT NULL THEN 1 ELSE 0 END) as inLibrary
    FROM GAME
    LEFT JOIN DEVELOPER ON GAME.developer_id = DEVELOPER.id_developer
    LEFT JOIN PUBLISHER ON GAME.publisher_id = PUBLISHER.id_publisher
    LEFT JOIN GAMEGENRE ON GAME.id_game = GAMEGENRE.game_id
    LEFT JOIN GENRE ON GAMEGENRE.genre_id = GENRE.id_genre
    LEFT JOIN GAMECATEGORY ON GAME.id_game = GAMECATEGORY.game_id
    LEFT JOIN CATEGORY ON GAMECATEGORY.category_id = CATEGORY.id_category
    LEFT JOIN GAMEPLATFORM ON GAME.id_game = GAMEPLATFORM.game_id
    LEFT JOIN PLATFORM ON GAMEPLATFORM.platform_id = PLATFORM.id_plataform
    LEFT JOIN LIBRARY_GAME ON (LIBRARY_GAME.library_id = ? AND LIBRARY_GAME.game_id = GAME.id_game)
    WHERE GAME.id_game = ?
    GROUP BY GAME.id_game
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $library_id, $game_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Game not found']);
    $stmt->close();
    exit;
}

$game = $result->fetch_assoc();
$stmt->close();

// Convert boolean values
$game['dlc_available'] = (bool)$game['dlc_available'];
$game['inLibrary'] = (bool)$game['inLibrary'];

echo json_encode([
    'success' => true,
    'game' => $game
]);
?>