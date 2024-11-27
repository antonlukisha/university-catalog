<?php
require_once '../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['group_id'], $_POST['leader_id'])) {
    $group_id = intval($_POST['group_id']);
    $student_id = intval($_POST['leader_id']);

    try {
        /*** TRANSACTION ***/
        $pdo->beginTransaction();

        // Get current leader
        $query_current_leader = "SELECT leader_id FROM study_groups WHERE group_id = :group_id;";
        $stmt_current_leader = $pdo->prepare($query_current_leader);
        $stmt_current_leader->execute(['group_id' => $group_id]);
        $current_leader = $stmt_current_leader->fetchColumn();

        // Delete current leader
        if ($current_leader) {
            $query_delete_leader = "DELETE FROM leaders WHERE leader_id = :leader_id;";
            $stmt_delete_leader = $pdo->prepare($query_delete_leader);
            $stmt_delete_leader->execute(['leader_id' => $current_leader]);
        }

        // Is student leader
        $query_check_leader = "SELECT leader_id FROM leaders WHERE student_id = :student_id;";
        $stmt_check_leader = $pdo->prepare($query_check_leader);
        $stmt_check_leader->execute(['student_id' => $student_id]);
        $existing_leader = $stmt_check_leader->fetchColumn();

        // Add student as leader
        if (!$existing_leader) {
            $query_insert_leader = "INSERT INTO leaders (student_id) VALUES (:student_id);";
            $stmt_insert_leader = $pdo->prepare($query_insert_leader);
            $stmt_insert_leader->execute(['student_id' => $student_id]);
            $leader_id = $pdo->lastInsertId();
        } else {
            $leader_id = $existing_leader;
        }

        // Update group
        $query_update_uroup = "UPDATE study_groups SET leader_id = :leader_id WHERE group_id = :group_id;";
        $stmt_update_group = $pdo->prepare($query_update_uroup);
        $stmt_update_group->execute([
            'leader_id' => $leader_id,
            'group_id' => $group_id
        ]);

        $pdo->commit();

        header("Location: group.php?group_id=" . urlencode($group_id));
        exit;
    } catch (Exception $exception) {
        // Rollback
        $pdo->rollBack();
        die("Ошибка: " . $exception->getMessage());
    }
}
?>
