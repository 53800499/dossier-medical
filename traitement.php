<?php
header('Content-Type: application/json');

// Vérifiez que la requête est bien une requête POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données envoyées
    $apprenant = $_POST['apprenant'] ?? '';
    $groupe_sanguin = $_POST['groupe_sanguin'] ?? '';
    $taille = $_POST['taille'] ?? '';
    $poids = $_POST['poids'] ?? '';

    // Conditions médicales
    $conditions_medicales = [];
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'condition_medicale_') === 0) {
            $conditions_medicales[] = $value;
        }
    }

    // Médicaments
    $medicaments = [];
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'medicament_') === 0) {
            $medicaments[] = $value;
        }
    }

    // Allergies
    $allergies = [];
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'allergie_') === 0) {
            $allergies[] = $value;
        }
    }

    // Autorisations
    $autorisations = [];
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'autorisation_') === 0) {
            $autorisations[] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }
    }

    // Réponse JSON
    echo json_encode([
        'status' => 'success',
        'message' => 'Données reçues avec succès',
        'data' => [
            'apprenant' => $apprenant,
            'groupe_sanguin' => $groupe_sanguin,
            'taille' => $taille,
            'poids' => $poids,
            'conditions_medicales' => $conditions_medicales,
            'medicaments' => $medicaments,
            'allergies' => $allergies,
            'autorisations' => $autorisations,
        ]
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Requête invalide'
    ]);
}
?>
