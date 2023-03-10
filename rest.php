<?php
session_start();
if (!isset($_SESSION['UserId'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Vous devez être connecté.']);
    exit;
}
require ('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['what'])) {
        switch ($_GET['what']){
            case "users":
                $query = $conn2->prepare("
                SELECT u.*, COALESCE(m.NotReadCount, 0) AS NotReadCount,
                       IFNULL(m2.LastMessageContent, '') AS LastMessage, 
                       IFNULL(m2.LastMessageDate, '') AS LastMessageDate
                FROM users u
                LEFT JOIN (
                    SELECT MessageExpediteur, COUNT(MessageLu) AS NotReadCount
                    FROM messages
                    WHERE MessageLu = 0
                    GROUP BY MessageExpediteur
                ) m ON u.UserId = m.MessageExpediteur
                LEFT JOIN (
                    SELECT 
                        CASE 
                            WHEN MessageExpediteur = u.UserId THEN MessageDestinataire
                            WHEN MessageDestinataire = u.UserId THEN MessageExpediteur
                        END AS CorrespondentId,
                        MAX(MessageDate) AS LastMessageDate,
                        SUBSTRING_INDEX(GROUP_CONCAT(MessageContent ORDER BY MessageDate DESC SEPARATOR '|'),'|',1) AS LastMessageContent
                    FROM messages
                    CROSS JOIN users u
                    WHERE MessageExpediteur = u.UserId OR MessageDestinataire = u.UserId
                    GROUP BY CorrespondentId
                ) m2 ON u.UserId = m2.CorrespondentId
                WHERE u.UserId != ? AND u.UserStatut = 0;

							");
//                $query = $conn2->prepare("
//                SELECT users.*, COALESCE(messages.NotReadCount, 0) as NotReadCount
//                FROM users
//                LEFT JOIN (
//                    SELECT MessageExpediteur, COUNT(MessageLu) as NotReadCount
//                    FROM messages
//                    WHERE MessageLu = 0
//                    GROUP BY MessageExpediteur
//                ) messages ON users.UserId = messages.MessageExpediteur
//                WHERE users.UserId != 1
//                AND users.UserStatut = 0;
//							");
//                $query = $conn2->prepare("
//                SELECT users.*, COALESCE(messages.NotReadCount, 0) as NotReadCount
//                FROM users
//                LEFT JOIN (
//                    SELECT MessageExpediteur, COUNT(MessageLu) as NotReadCount
//                    FROM messages
//                    WHERE MessageLu = 0
//                    GROUP BY MessageExpediteur
//                ) messages ON users.UserId = messages.MessageExpediteur
//                WHERE users.UserId != ?
//                AND users.UserStatut = 0;
//							");
                $query->bindValue(1, $_SESSION['UserId']);
                $query->execute();
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($result);
            case "messages":
                if (isset($_GET['who'])) {
                    $_GET['who'] = intval($_GET['who']);

                    $query = $conn2->prepare("
                        UPDATE messages SET MessageLu = 1 WHERE MessageExpediteur = ? and MessageDestinataire = ?;
							");
                    $query->bindValue(1, $_GET['who']);
                    $query->bindValue(2, $_SESSION['UserId']);
                    $query->execute();

                    $query = $conn2->prepare("
                        SELECT * FROM messages WHERE (MessageExpediteur = ? or MessageDestinataire = ?) AND (MessageExpediteur = ? or MessageDestinataire = ?) ORDER BY messages.MessageDate ASC ;
							");
                    $query->bindValue(1, $_GET['who']);
                    $query->bindValue(2, $_GET['who']);
                    $query->bindValue(3, $_SESSION['UserId']);
                    $query->bindValue(4, $_SESSION['UserId']);
                    $query->execute();
                    $result = $query->fetchAll(PDO::FETCH_ASSOC);
                    echo json_encode($result);
                }
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Paramètre "what" invalide']);
    }
}elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postData = json_decode(file_get_contents('php://input'), true);
    if ($postData['what'] === 'addMessage') {
        // Récupérer les données envoyées dans la requête POST
        $postData = json_decode(file_get_contents('php://input'), true);
        if (!isset($postData['data']['user_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Paramètre manquant : user_id']);
            exit;
        }
        if (!isset($postData['data']['message'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Paramètre manquant : message']);
            exit;
        }
        $userId = $postData['data']['user_id'];
        $message = $postData['data']['message'];

        $newId = $conn2->query("SELECT MAX(MessageId) + 1 FROM messages")->fetchColumn();

        $query = $conn2->prepare("
                INSERT INTO messages (MessageId, MessageExpediteur, MessageDestinataire, MessageContent) VALUES (?, ?, ?, ?);
							");
        $query->bindValue(1, $newId);
        $query->bindValue(2, $_SESSION['UserId']);
        $query->bindValue(3, $userId);
        $query->bindValue(4, $message);
        $query->execute();

        $query = $conn2->prepare("SELECT * FROM messages WHERE MessageId = ?");
        $query->bindValue(1, $newId);
        $query->execute();
        $message = $query->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'msg' => $message[0]]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Paramètre "what" invalide']);
    }
}