 (cd "$(git rev-parse --show-toplevel)" && git apply --3way <<'EOF' 
diff --git a/Examen Final.2025/controllers/AdminController.php	 b/Examen Final.2025/controllers/AdminController.php	
index 5ad3e065e51b2d9bda456d1ec7c1e8be225b592e..45d2af34773ca646438698ff68ce43e58f146bad 100644
--- a/Examen Final.2025/controllers/AdminController.php	
+++ b/Examen Final.2025/controllers/AdminController.php	
@@ -96,50 +96,57 @@ class AdminController {
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
+        if (session_status() === PHP_SESSION_NONE) {
+            session_start();
+        }
+        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
+            header("Location: index.php?route=home");
+            exit();
+        }
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
diff --git a/Examen Final.2025/controllers/AdminController.php	 b/Examen Final.2025/controllers/AdminController.php	
index 5ad3e065e51b2d9bda456d1ec7c1e8be225b592e..45d2af34773ca646438698ff68ce43e58f146bad 100644
--- a/Examen Final.2025/controllers/AdminController.php	
+++ b/Examen Final.2025/controllers/AdminController.php	
@@ -147,77 +154,93 @@ class AdminController {
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
+        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
+            header("Location: index.php?route=home");
+            exit();
+        }
         $modelArticles = new \Models\Articles();
         $modelArticles->deleteArticle($id);
         header("Location: index.php?route=admin");
         exit();
     }
     
     public function deleteUser($id) {
         if (session_status() === PHP_SESSION_NONE) {
             session_start();
         }
+        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
+            header("Location: index.php?route=home");
+            exit();
+        }
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
+        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
+            header("Location: index.php?route=home");
+            exit();
+        }
         $cartModel = new \Models\Cart();
         $cartItems = $cartModel->getCartByUserId($userId);
         $template = "viewUserCart.phtml";
         include_once 'views/layout.phtml';
     }
     
     
     public function changeOrderStatus() {
-    if (session_status() === PHP_SESSION_NONE) {
+        if (session_status() === PHP_SESSION_NONE) {
             session_start();
         }
+        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
+            header("Location: index.php?route=home");
+            exit();
+        }
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
 
EOF
)
