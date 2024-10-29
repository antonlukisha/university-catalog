<?php
require_once 'includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['group_id']) && isset($_POST['leader_id'])) {
    $group_id = intval($_POST['group_id']);
    $student_id = intval($_POST['leader_id']);

    $Q_current_leader = "SELECT leader_id FROM study_groups WHERE group_id = :group_id;";
    $stmt_current = $pdo->prepare($Q_current_leader);
    $stmt_current->execute(['group_id' => $group_id]);
    $current_leader = $stmt_current->fetch(PDO::FETCH_ASSOC);

    if ($current_leader && $current_leader['leader_id']) {
        $Q_delete_old_leader = "DELETE FROM leaders WHERE leader_id = :leader_id;";
        $stmt_delete = $pdo->prepare($Q_delete_old_leader);
        $stmt_delete->execute(['leader_id' => $current_leader['leader_id']]);
    }

    $Q_check_leader = "SELECT * FROM leaders WHERE student_id = :student_id;";
    $stmt_check = $pdo->prepare($Q_check_leader);
    $stmt_check->execute(['student_id' => $student_id]);
    $existing_leader = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if (!$existing_leader) {
        $Q_insert_leader = "INSERT INTO leaders (student_id) VALUES (:student_id);";
        $stmt_insert = $pdo->prepare($Q_insert_leader);
        $stmt_insert->execute(['student_id' => $student_id]);
    }

    $Q_update_group = "UPDATE study_groups SET leader_id = (SELECT leader_id FROM leaders WHERE student_id = :student_id) WHERE group_id = :group_id;";
    $stmt_update = $pdo->prepare($Q_update_group);
    $stmt_update->execute([
        'student_id' => $student_id, 
        'group_id' => $group_id
    ]);

    header("Location: group.php?group_id=" . urlencode($group_id));
    exit;
}
?>
