<?php
session_start();

if (!isset($_SESSION['admin_logged_in_thinkplusbd']) || $_SESSION['admin_logged_in_thinkplusbd'] !== true) {
    header("Location: admin_login.php");
    exit();
}

$categories_file_path = __DIR__ . '/categories.json';

function read_categories() {
    global $categories_file_path;
    if (!file_exists($categories_file_path)) {
        return [];
    }
    $json_data = file_get_contents($categories_file_path);
    return json_decode($json_data, true);
}

function write_categories($categories) {
    global $categories_file_path;
    file_put_contents($categories_file_path, json_encode($categories, JSON_PRETTY_PRINT));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_category'])) {
        $new_category_name = trim($_POST['category_name']);
        $new_category_icon = trim($_POST['category_icon']);
        $new_category_subtitle = trim($_POST['category_subtitle']);

        if (!empty($new_category_name) && !empty($new_category_icon) && !empty($new_category_subtitle)) {
            $categories = read_categories();
            $new_category = [
                'name' => $new_category_name,
                'icon' => $new_category_icon,
                'subtitle' => $new_category_subtitle
            ];
            $categories[] = $new_category;
            write_categories($categories);
        }
    } elseif (isset($_POST['delete_category'])) {
        $category_name_to_delete = $_POST['category_name'];
        $categories = read_categories();
        $categories = array_filter($categories, function($category) use ($category_name_to_delete) {
            return $category['name'] !== $category_name_to_delete;
        });
        write_categories(array_values($categories));
    }
}

header("Location: admin_dashboard.php#manage-categories");
exit();
?>
