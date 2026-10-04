<?php
require_once __DIR__ . '/config.php';

if (isset($_GET['del1']) && $_GET['del1'] !== '') {

    $carno = trim($_GET['del1']);

    if (!is_numeric($carno)) {
        header("Location: index.php?msg=error");
        exit;
    }

    try {

        $response = supabaseRequest(
            'DELETE',
            '/rest/v1/park1?carno=eq.' . rawurlencode($carno),
            null,
            ['Prefer: return=representation']
        );

        if ($response['status'] >= 200 && $response['status'] < 300) {
            header("Location: index.php?msg=deleted");
            exit;
        }

        die("Unable to delete car: " .
            htmlspecialchars(
                $response['body']['message'] ?? $response['raw'],
                ENT_QUOTES,
                'UTF-8'
            )
        );

    } catch (Throwable $e) {
        die("Unable to delete car: " .
            htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')
        );
    }
}

header("Location: index.php");
exit;
?>
