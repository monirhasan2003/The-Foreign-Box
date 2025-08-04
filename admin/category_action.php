<?php
// This script handles all actions related to categories: Add, Update, and Delete.
session_start();
require_once '../../config/database.php';

// Determine the action based on POST or GET requests.
$action = $_POST['action'] ?? $_GET['action'] ?? null;

// Use a switch statement to handle different actions
switch ($action) {
    case 'add':
        add_category($conn);
        break;
    
    case 'update':
        // We will build this logic after creating the edit_category.php page.
        // For now, we can leave a placeholder.
        update_category($conn);
        break;

    case 'delete':
        delete_category($conn);
        break;

    default:
        // If no valid action is provided, redirect with an error.
        $_SESSION['error_message'] = "Invalid action specified.";
        header('Location: ../categories.php');
        exit();
}

/**
 * Handles adding a new category.
 */
function add_category($conn) {
    // Sanitize and retrieve form data
    $name = trim($_POST['name']);
    $slug = trim($_POST['slug']);
    // Handle parent_id: if it's empty, it should be NULL in the database.
    $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : NULL;

    // Basic validation
    if (empty($name) || empty($slug)) {
        $_SESSION['error_message'] = "Category Name and Slug are required.";
        header('Location: ../categories.php');
        exit();
    }

    try {
        $stmt = $conn->prepare("INSERT INTO categories (name, slug, parent_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $name, $slug, $parent_id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Category added successfully!";
        } else {
            // Check for duplicate slug error
            if ($conn->errno == 1062) { // 1062 is the MySQL error code for duplicate entry
                throw new Exception("The slug '$slug' already exists. Please choose a unique one.");
            } else {
                throw new Exception($stmt->error);
            }
        }
        $stmt->close();
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error adding category: " . $e->getMessage();
    }

    $conn->close();
    header('Location: ../categories.php');
    exit();
}

/**
 * Handles deleting an existing category.
 */
function delete_category($conn) {
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        $_SESSION['error_message'] = "Invalid category ID.";
        header('Location: ../categories.php');
        exit();
    }
    $category_id = (int)$_GET['id'];

    try {
        // The database schema is set up with 'ON DELETE SET NULL' for the parent_id.
        // This means if we delete a parent category, its child categories will automatically
        // become top-level categories (their parent_id will be set to NULL).
        // This prevents data loss and maintains integrity.
        $stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
        $stmt->bind_param("i", $category_id);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Category deleted successfully.";
        } else {
            throw new Exception($stmt->error);
        }
        $stmt->close();
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error deleting category: " . $e->getMessage();
    }
    
    $conn->close();
    header('Location: ../categories.php');
    exit();
}

/**
 * Handles updating an existing category.
 * We will implement this function's logic later.
 */
function update_category($conn) {
    // Placeholder for update logic
    $category_id = (int)$_POST['category_id'];
    $name = trim($_POST['name']);
    $slug = trim($_POST['slug']);
    $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : NULL;

    // Basic validation
    if (empty($name) || empty($slug) || empty($category_id)) {
        $_SESSION['error_message'] = "Missing required fields for update.";
        header('Location: ../categories.php');
        exit();
    }

    try {
        $stmt = $conn->prepare("UPDATE categories SET name = ?, slug = ?, parent_id = ? WHERE category_id = ?");
        $stmt->bind_param("ssii", $name, $slug, $parent_id, $category_id);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Category updated successfully!";
        } else {
            if ($conn->errno == 1062) {
                throw new Exception("The slug '$slug' already exists. Please choose a unique one.");
            } else {
                throw new Exception($stmt->error);
            }
        }
        $stmt->close();
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error updating category: " . $e->getMessage();
    }
    
    $conn->close();
    header('Location: ../categories.php');
    exit();
}

?>
