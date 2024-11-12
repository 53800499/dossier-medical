<?php
header("Content-Type: application/json");

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "medecin";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Erreur de connexion : " . $e->getMessage()]);
    exit;
}

// Création des tables si elles n'existent pas
try {
    // Table apprenants
    $pdo->exec("CREATE TABLE IF NOT EXISTS apprenants (
        id INT AUTO_INCREMENT PRIMARY KEY,
        apprenant VARCHAR(255) NOT NULL,
        groupe_sanguin VARCHAR(5),
        taille FLOAT,
        poids FLOAT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Table conditions_medicales
    $pdo->exec("CREATE TABLE IF NOT EXISTS conditions_medicales (
        id INT AUTO_INCREMENT PRIMARY KEY,
        apprenant_id INT,
        condition VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (apprenant_id) REFERENCES apprenants(id) ON DELETE CASCADE
    )");

    // Table medicaments
    $pdo->exec("CREATE TABLE IF NOT EXISTS medicaments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        apprenant_id INT,
        medicament VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (apprenant_id) REFERENCES apprenants(id) ON DELETE CASCADE
    )");

    // Table allergies
    $pdo->exec("CREATE TABLE IF NOT EXISTS allergies (
        id INT AUTO_INCREMENT PRIMARY KEY,
        apprenant_id INT,
        allergie VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (apprenant_id) REFERENCES apprenants(id) ON DELETE CASCADE
    )");

    // Table autorisations
    $pdo->exec("CREATE TABLE IF NOT EXISTS autorisations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        apprenant_id INT,
        autorisation BOOLEAN,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (apprenant_id) REFERENCES apprenants(id) ON DELETE CASCADE
    )");

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Erreur lors de la création des tables : " . $e->getMessage()]);
    exit;
}

// Récupération des données du formulaire
$apprenant = $_POST['apprenant'] ?? '';
$groupe_sanguin = $_POST['groupe_sanguin'] ?? '';
$taille = $_POST['taille'] ?? '';
$poids = $_POST['poids'] ?? '';

$conditions_medicales = [];
$medicaments = [];
$allergies = [];
$autorisations = [];

// Récupération dynamique des données répétées
foreach ($_POST as $key => $value) {
    if (strpos($key, 'condition_medicale_') === 0) {
        $conditions_medicales[] = $value;
    }
    if (strpos($key, 'medicament_') === 0) {
        $medicaments[] = $value;
    }
    if (strpos($key, 'allergie_') === 0) {
        $allergies[] = $value;
    }
    if (strpos($key, 'autorisation_') === 0) {
        $autorisations[] = $value;
    }
}

try {
    // Insertion des données dans la table apprenants
    $sql = "INSERT INTO apprenants (apprenant, groupe_sanguin, taille, poids) VALUES (:apprenant, :groupe_sanguin, :taille, :poids)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':apprenant' => $apprenant,
        ':groupe_sanguin' => $groupe_sanguin,
        ':taille' => $taille,
        ':poids' => $poids
    ]);

    // Récupération de l'ID pour les tables associées
    $apprenant_id = $pdo->lastInsertId();

    // Insertion dans la table conditions_medicales
    $sql = "INSERT INTO conditions_medicales (apprenant_id, condition) VALUES (:apprenant_id, :condition)";
    $stmt = $pdo->prepare($sql);
    foreach ($conditions_medicales as $condition) {
        $stmt->execute([':apprenant_id' => $apprenant_id, ':condition' => $condition]);
    }

    // Insertion dans la table medicaments
    $sql = "INSERT INTO medicaments (apprenant_id, medicament) VALUES (:apprenant_id, :medicament)";
    $stmt = $pdo->prepare($sql);
    foreach ($medicaments as $medicament) {
        $stmt->execute([':apprenant_id' => $apprenant_id, ':medicament' => $medicament]);
    }

    // Insertion dans la table allergies
    $sql = "INSERT INTO allergies (apprenant_id, allergie) VALUES (:apprenant_id, :allergie)";
    $stmt = $pdo->prepare($sql);
    foreach ($allergies as $allergie) {
        $stmt->execute([':apprenant_id' => $apprenant_id, ':allergie' => $allergie]);
    }

    // Insertion dans la table autorisations
    $sql = "INSERT INTO autorisations (apprenant_id, autorisation) VALUES (:apprenant_id, :autorisation)";
    $stmt = $pdo->prepare($sql);
    foreach ($autorisations as $autorisation) {
        $stmt->execute([':apprenant_id' => $apprenant_id, ':autorisation' => (int)$autorisation]);
    }

    // Réponse JSON de succès
    echo json_encode(["status" => "success", "message" => "Données insérées avec succès"]);
} catch (Exception $e) {
    // En cas d'erreur
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
