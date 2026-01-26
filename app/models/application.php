<?php

namespace App\models;

use App\core\Database;

class Application
{
    public function create(int $studentId, int $announcementId, string $message): void
    {
        Database::query(
            "INSERT INTO applications (student_id, announcement_id, message, status)
             VALUES (:student_id, :announcement_id, :message, 'pending')",
            [
                'student_id' => $studentId,
                'announcement_id' => $announcementId,
                'message' => $message
            ]
        );
    }

    public function exists(int $studentId, int $announcementId): bool
    {
        $stmt = Database::query(
            "SELECT id FROM applications
             WHERE student_id = :student_id AND announcement_id = :announcement_id
             LIMIT 1",
            [
                'student_id' => $studentId,
                'announcement_id' => $announcementId
            ]
        );

        return (bool)$stmt->fetch();
    }

    public function jobIdsForStudent(int $studentId): array
    {
        $stmt = Database::query(
            "SELECT announcement_id FROM applications
             WHERE student_id = :student_id
             ORDER BY announcement_id",
            [
                'student_id' => $studentId
            ]
        );

        return array_map('intval', $stmt->fetchAll(\PDO::FETCH_COLUMN));
    }

    public function statusesForStudent(int $studentId): array
    {
        $stmt = Database::query(
            "SELECT announcement_id, status
             FROM applications
             WHERE student_id = :student_id",
            [
                'student_id' => $studentId
            ]
        );

        $rows = $stmt->fetchAll();
        $map = [];
        foreach ($rows as $row) {
            $map[(int)$row['announcement_id']] = (string)$row['status'];
        }
        return $map;
    }

    public function allWithDetails(): array
    {
        $stmt = Database::query(
            "SELECT a.id,
                    a.status,
                    a.message,
                    a.created_at,
                    s.id AS student_id,
                    u.name AS student_name,
                    u.email AS student_email,
                    ann.id AS announcement_id,
                    ann.title AS job_title,
                    c.name AS company_name
             FROM applications a
             JOIN students s ON s.id = a.student_id
             JOIN users u ON u.id = s.user_id
             JOIN announcements ann ON ann.id = a.announcement_id
             JOIN companies c ON c.id = ann.company_id
             ORDER BY a.created_at DESC"
        );

        return $stmt->fetchAll();
    }

    public function updateStatus(int $id, string $status): void
    {
        Database::query(
            "UPDATE applications
             SET status = :status
             WHERE id = :id",
            [
                'status' => $status,
                'id' => $id
            ]
        );
    }
}
