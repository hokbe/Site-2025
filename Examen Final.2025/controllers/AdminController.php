<?php
namespace Controllers;

class AdminController {
    
    public function displayDashboard() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header("Location: index.php?route=home");
            exit();
        }
        
        $modelArticles = new \Models\Articles();
        $articles = $modelArticles->getAllArticles();
        
        $modelUsers = new \Models\Users();
        $users = $modelUsers->getAllUsers();
        
        $modelOrder = new \Models\Order();
        $orders = $modelOrder->getAllOrders();
        
        $template = "adminDashboard.phtml";
        include_once 'views/layout.phtml';
    }
    public function addArticle() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header("Location: index.php?route=home");
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $modelCategories = new \Models\Categories();
            $categories = $modelCategories->getAllCategories();
            $template = "addArticleForm.phtml";
            include_once 'views/layout.phtml';
        } else {
            $data = [
                'title'         => trim($_POST['title']),
                'author'        => trim($_POST['author']),
                'price'         => trim($_POST['price']),
                'description'   => trim($_POST['description']),
                'alt'           => trim($_POST['alt']),
                'type'          => trim($_POST['type']),
                'stock'         => trim($_POST['stock']),
                'id_categories' => intval($_POST['id_categories'])
            ];
            
            // Gestion de l'upload d'image
            if (!empty($_FILES['image']['name'])) {
                $uploadDir = 'Public/images/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $fileName   = time() . '_' . basename($_FILES['image']['name']);
                $targetPath = $uploadDir . $fileName;
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($_FILES['image']['type'], $allowedTypes)) {
                    $errorMessage = "Type de fichier non autorisé.";
                    $modelCategories = new \Models\Categories();
                    $categories = $modelCategories->getAllCategories();
                    $template = "addArticleForm.phtml";
                    include_once 'views/layout.phtml';
                    exit();
                }
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $data['image'] = $targetPath;
                } else {
                    $errorMessage = "Erreur lors de l'upload de l'image.";
                    $modelCategories = new \Models\Categories();
                    $categories = $modelCategories->getAllCategories();
                    $template = "addArticleForm.phtml";
                    include_once 'views/layout.phtml';
                    exit();
                }
            } else {
                $data['image'] = 'Public/images/default.png';
            }
            
            $modelArticles = new \Models\Articles();
            $result = $modelArticles->createArticle($data);
            if ($result) {
                header("Location: index.php?route=admin");
                exit();
            } else {
                $errorMessage = "Une erreur est survenue lors de l'ajout de l'article.";
                $modelCategories = new \Models\Categories();
                $categories = $modelCategories->getAllCategories();
                $template = "addArticleForm.phtml";
                include_once 'views/layout.phtml';
            }
        }
    }

 
    public function editArticle($id) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header("Location: index.php?route=home");
            exit();
        }
        
        $modelArticles = new \Models\Articles();
        $article = $modelArticles->getOneArticle($id);
        
        $modelCategories = new \Models\Categories();
        $categories = $modelCategories->getAllCategories();
        
        $template = "editArticleForm.phtml";
        include_once 'views/layout.phtml';
    }
    
    public function updateArticle() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = intval($_POST['id']);
            $data = [
                'title'       => $_POST['title'],
                'author'      => $_POST['author'],
                'price'       => $_POST['price'],
                'description' => $_POST['description'],
                'alt'         => $_POST['alt'],
                'type'        => $_POST['type'],
                'stock'       => $_POST['stock'],
                'id_categories' => $_POST['id_categories']
            ];
            // upload image
            if (!empty($_FILES['image']['name'])) {
                $uploadDir = 'Public/images/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                // Préfixe le nom de fichier par un timestamp pour éviter les doublons
                $fileName = time() . '_' . basename($_FILES['image']['name']);
                $targetPath = $uploadDir . $fileName;
                // Vérification du type MIME (seulement les images)
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($_FILES['image']['type'], $allowedTypes)) {
                    echo "Type de fichier non autorisé.";
                    exit();
                }
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $data['image'] = $targetPath;
                } else {
                    echo "Erreur lors de l'upload de l'image.";
                    exit();
                }
            } else {
                // Conserver l'image actuelle si aucune nouvelle image n'est uploadée
                $modelArticles = new \Models\Articles();
                $currentArticle = $modelArticles->getOneArticle($id);
                $data['image'] = $currentArticle['image'];
            }
            
            $modelArticles = new \Models\Articles();
            $modelArticles->updateArticle($id, $data);
            header("Location: index.php?route=admin");
            exit();
        }
    }
    
    public function deleteArticle($id) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $modelArticles = new \Models\Articles();
        $modelArticles->deleteArticle($id);
        header("Location: index.php?route=admin");
        exit();
    }
    
    public function deleteUser($id) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $modelUsers = new \Models\Users();
        $user = $modelUsers->getUserById($id);
        if ($user && $user['role'] !== 'admin') {
            $modelUsers->deleteUser($id);
        }
        header("Location: index.php?route=admin");
        exit();
    }
    
    public function viewUserCart($userId) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $cartModel = new \Models\Cart();
        $cartItems = $cartModel->getCartByUserId($userId);
        $template = "viewUserCart.phtml";
        include_once 'views/layout.phtml';
    }
    
    
    public function changeOrderStatus() {
    if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id']) || !isset($_POST['status'])) {
            header("Location: index.php?route=admin");
            exit();
        }
        $orderId = intval($_POST['id']);
        $status = trim($_POST['status']);
        $validStatus = ['paid', 'in preparation', 'shipped', 'canceled', 'completed'];
        if (!in_array($status, $validStatus)) {
            header("Location: index.php?route=admin");
            exit();
        }
        $modelOrder = new \Models\Order();
        $modelOrder->updateOrderStatus($orderId, $status);
        header("Location: index.php?route=admin");
        exit();
    }
}
?>
