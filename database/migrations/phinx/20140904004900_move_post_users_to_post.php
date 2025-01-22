<?php

use Phinx\Migration\AbstractMigration;

class MovePostUsersToPost extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {
        $pdo = $this->getAdapter()->getConnection();

        // Get existing user info
        $rows = $this->fetchAll(
            "SELECT email, realname, posts.id AS post_id
            FROM users
            INNER JOIN posts ON (posts.user_id = users.id)
            WHERE username IS NULL"
        );

        $update_posts = $pdo->prepare(
            "UPDATE posts
            SET
                author_email = :email,
                author_realname = :realname,
                user_id = NULL
            WHERE
                id = :id"
        );

        foreach ($rows as $row) {
            // Save author info onto post, and remove user_id
            // Using PDO prepared statement until https://github.com/robmorgan/phinx/pull/205 lands
            $update_posts->execute(
                [
                    ':email' => $row['email'],
                    ':realname' => $row['realname'],
                    ':id' => $row['post_id']
                ]
            );
        }

        // Delete unregistered users
        $this->execute("DELETE FROM users WHERE username IS NULL");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        // Get post author info
        $rows = $this->fetchAll(
            "SELECT DISTINCT author_email, author_realname
            FROM posts
            WHERE
                user_id IS NULL AND
                author_email IS NOT NULL AND
                author_email <> ''"
        );

        $pdo = $this->getAdapter()->getConnection();

        $insert_users = $pdo->prepare("INSERT INTO users (email, realname, role) VALUES (:email, :realname, :role)");

        $update_posts = $pdo->prepare("UPDATE posts SET user_id = :user_id WHERE author_email = :email");

        foreach ($rows as $row) {
            // Create unregistered users for posts
            $insert_users->execute(
                [
                    ':email' => $row['author_email'],
                    ':realname' => $row['author_realname'],
                    ':role' => 'user'
                ]
            );

            $user_id = $pdo->lastInsertId();

            // Set post user_id with new user id
            $update_posts->execute(
                [
                    ':user_id' => $user_id,
                    ':email' => $row['author_email']
                ]
            );
        }
    }
}
