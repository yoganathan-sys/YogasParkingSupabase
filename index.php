<?php
require_once __DIR__ . '/config.php';

$error_message = '';
$success_message = '';

/* =========================================================
   ADD NEW CAR
   ========================================================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {

    $carno  = trim($_POST['carno'] ?? '');
    $carn   = trim($_POST['carn'] ?? '');
    $caro   = trim($_POST['caro'] ?? '');
    $charge = trim($_POST['charge'] ?? '');

    if ($carno === '' || $carn === '' || $caro === '' || $charge === '') {
        $error_message = "All fields are required.";
    } elseif (!is_numeric($carno) || !is_numeric($charge)) {
        $error_message = "Car code and charge must be numeric.";
    } else {
        try {
            $response = supabaseRequest(
                'POST',
                '/rest/v1/park1',
                [
                    'carno' => (int)$carno,
                    'carn' => $carn,
                    'caro' => $caro,
                    'charge' => (float)$charge
                ],
                ['Prefer: return=representation']
            );

            if ($response['status'] >= 200 && $response['status'] < 300) {
                header("Location: index.php?msg=added");
                exit;
            }

            $message = $response['body']['message'] ?? $response['raw'];
            $error_message = "Unable to add car: " . $message;

        } catch (Throwable $e) {
            $error_message = "Unable to add car: " . $e->getMessage();
        }
    }
}

/* =========================================================
   UPDATE CAR
   ========================================================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {

    $carno  = trim($_POST['carno'] ?? '');
    $carn   = trim($_POST['carn'] ?? '');
    $caro   = trim($_POST['caro'] ?? '');
    $charge = trim($_POST['charge'] ?? '');

    if ($carno === '' || $carn === '' || $caro === '' || $charge === '') {
        $error_message = "All fields are required.";
    } elseif (!is_numeric($carno) || !is_numeric($charge)) {
        $error_message = "Car code and charge must be numeric.";
    } else {
        try {
            $response = supabaseRequest(
                'PATCH',
                '/rest/v1/park1?carno=eq.' . rawurlencode($carno),
                [
                    'carn' => $carn,
                    'caro' => $caro,
                    'charge' => (float)$charge
                ],
                ['Prefer: return=representation']
            );

            if ($response['status'] >= 200 && $response['status'] < 300) {
                header("Location: index.php?msg=updated");
                exit;
            }

            $message = $response['body']['message'] ?? $response['raw'];
            $error_message = "Unable to update car: " . $message;

        } catch (Throwable $e) {
            $error_message = "Unable to update car: " . $e->getMessage();
        }
    }
}

/* =========================================================
   SUCCESS MESSAGE
   ========================================================= */
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'added') {
        $success_message = "Car added successfully.";
    } elseif ($_GET['msg'] === 'updated') {
        $success_message = "Car updated successfully.";
    } elseif ($_GET['msg'] === 'deleted') {
        $success_message = "Car deleted successfully.";
    }
}

/* =========================================================
   GET PARKING DATA
   ========================================================= */
$parkingRows = [];

try {
    $response = supabaseRequest(
        'GET',
        '/rest/v1/park1?select=carno,carn,caro,charge&order=carno.asc'
    );

    if ($response['status'] >= 200 && $response['status'] < 300) {
        $parkingRows = is_array($response['body']) ? $response['body'] : [];
    } else {
        $message = $response['body']['message'] ?? $response['raw'];
        $error_message = "Unable to load parking data: " . $message;
    }

} catch (Throwable $e) {
    $error_message = "Unable to load parking data: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Yoga's Parking System</title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>

    <script src="https://code.jquery.com/ui/1.11.3/jquery-ui.min.js"></script>

    <style>
        body {
            background:
            linear-gradient(
                rgba(8, 10, 15, 0.65),
                rgba(8, 10, 15, 0.78)
            ),
            url("car-1.jpeg")
            no-repeat
            center
            center
            fixed;

            background-size: cover;
            min-height: 100vh;
            margin: 0;

            font-family:
            'Poppins',
            -apple-system,
            BlinkMacSystemFont,
            'Segoe UI',
            Roboto,
            sans-serif;

            color: #e2e8f0;
        }

        #parkingCard {
            cursor: move;
        }

        #parkingCard .card-body,
        #parkingCard input,
        #parkingCard button,
        #parkingCard a {
            cursor: default;
        }

        .card-header {
            background-color: #000000;
        }

        .parking-title {
            color: red;
            text-align: center;

            text-shadow:
            0 0 5px red,
            0 0 10px red,
            0 0 20px red;
        }

        .add-button {
            background-color: #ff0000;
            border: none;
            border-radius: 30px;
            padding: 12px 30px;
            font-weight: bold;

            box-shadow:
            0 0 10px red,
            0 0 20px red;
        }

        .add-button:hover {
            background-color: #cc0000;
        }

        .parking-body {
            background-color: rgba(144, 238, 144, 0.95);
        }

        .table {
            margin-bottom: 0;
        }

        .table input {
            border-radius: 5px;
            border: 1px solid #555;
            padding: 5px;
        }

        .modal-header {
            background-color: green;
            color: white;
        }

        .modal-body {
            background-color: red;
        }

        .modal-footer {
            background-color: blue;
        }

        .btn-register {
            background-color: white;
            color: red;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
        }

        .error-message {
            background-color: #8b0000;
            color: white;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .success-message {
            background-color: #006400;
            color: white;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
    </style>
</head>

<body>

<div class="container">

    <div class="card" id="parkingCard">

        <!-- HEADER -->
        <div class="card-header text-white">

            <h1 class="parking-title">
                <u>
                    <b>
                        YOGA'S PARKING SYSTEM
                    </b>
                </u>
            </h1>

            <div style="text-align:center;">

                <button
                    type="button"
                    id="demo"
                    data-toggle="modal"
                    data-target="#test"
                    class="p-2 mb-3 btn btn-primary add-button">

                    <a href="#"
                       class="text-light"
                       style="text-decoration:none;"
                       onclick="return false;">

                        <b>⊕ ADD NEW</b>

                    </a>

                </button>

            </div>

        </div>

        <!-- BODY -->
        <div class="card-body parking-body">

            <?php if ($error_message !== ''): ?>

                <div class="error-message">
                    <?= htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8') ?>
                </div>

            <?php endif; ?>

            <?php if ($success_message !== ''): ?>

                <div class="success-message">
                    <?= htmlspecialchars($success_message, ENT_QUOTES, 'UTF-8') ?>
                </div>

            <?php endif; ?>

            <!-- PARKING TABLE -->
            <table id="parkingTable"
                   class="table table-hover table-bordered table-dark">

                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Car No</th>
                        <th>Car Name</th>
                        <th>Car Owner</th>
                        <th>Charges (Rs.)</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody style="
                    color:black;
                    font-size:14pt;
                    font-family:Cursive, Arial, sans-serif;
                ">

                <?php $sno = 1; ?>

                <?php foreach ($parkingRows as $row): ?>

                    <?php
                        $carno  = $row['carno'] ?? '';
                        $carn   = $row['carn'] ?? '';
                        $caro   = $row['caro'] ?? '';
                        $charge = $row['charge'] ?? '';
                    ?>

                    <tr class="table-secondary">

                        <!-- S.NO -->
                        <td>
                            <?= $sno++ ?>
                        </td>

                        <!-- CAR NUMBER -->
                        <td>
                            <input
                                type="text"
                                name="carno"
                                size="5"
                                value="<?= htmlspecialchars((string)$carno, ENT_QUOTES, 'UTF-8') ?>"
                                readonly>
                        </td>

                        <!-- CAR NAME -->
                        <td>
                            <input
                                type="text"
                                name="carn"
                                size="10"
                                value="<?= htmlspecialchars((string)$carn, ENT_QUOTES, 'UTF-8') ?>"
                                readonly>
                        </td>

                        <!-- CAR OWNER -->
                        <td>
                            <input
                                type="text"
                                name="caro"
                                size="10"
                                value="<?= htmlspecialchars((string)$caro, ENT_QUOTES, 'UTF-8') ?>"
                                readonly>
                        </td>

                        <!-- CHARGE -->
                        <td>
                            <input
                                type="text"
                                name="charge"
                                size="8"
                                value="<?= htmlspecialchars((string)$charge, ENT_QUOTES, 'UTF-8') ?>"
                                readonly>
                        </td>

                        <!-- ACTION -->
                        <td>

                            <!-- UPDATE -->
                            <form
                                action=""
                                method="post"
                                style="display:inline-block;">

                                <input
                                    type="hidden"
                                    name="carno"
                                    value="<?= htmlspecialchars((string)$carno, ENT_QUOTES, 'UTF-8') ?>">

                                <input
                                    type="hidden"
                                    name="carn"
                                    value="<?= htmlspecialchars((string)$carn, ENT_QUOTES, 'UTF-8') ?>">

                                <input
                                    type="hidden"
                                    name="caro"
                                    value="<?= htmlspecialchars((string)$caro, ENT_QUOTES, 'UTF-8') ?>">

                                <input
                                    type="hidden"
                                    name="charge"
                                    value="<?= htmlspecialchars((string)$charge, ENT_QUOTES, 'UTF-8') ?>">

                                <button
                                    type="submit"
                                    class="btn btn-secondary"
                                    name="edit"
                                    onclick="return confirm('Are You Edit?')">

                                    UPDATE

                                </button>

                            </form>

                            <!-- DELETE -->
                            <a
                                href="del1.php?del1=<?= urlencode((string)$carno) ?>"
                                class="btn btn-danger"
                                style="margin-left:10px;"
                                onclick="return confirm('Are You Delete?')">

                                DELETE

                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>
            </table>

            <!-- FOOTER -->
            <div class="card-footer"
                 style="color:red;">

                <?php date_default_timezone_set("Asia/Kolkata"); ?>

                <h6>
                    <?= date("d/M/Y") ?>
                    <br>
                    <?= date("h:i:sa") ?>
                </h6>

                <h6 align="right">
                    Thank you! Visit again!!
                </h6>

            </div>

        </div>
    </div>
</div>

<!-- ADD NEW CAR MODAL -->
<div class="modal"
     id="test"
     tabindex="-1"
     role="dialog">

    <div class="modal-dialog"
         role="document">

        <div class="modal-content">

            <!-- MODAL HEADER -->
            <div class="modal-header">

                <h5 class="modal-title">
                    Add New Car Details
                </h5>

                <button
                    type="button"
                    class="close"
                    data-dismiss="modal"
                    aria-label="Close">

                    <span aria-hidden="true">
                        &times;
                    </span>

                </button>

            </div>

            <!-- MODAL BODY -->
            <div class="modal-body">

                <form
                    action=""
                    method="post">

                    <!-- CAR NUMBER -->
                    <div class="form-group">

                        <input
                            type="number"
                            name="carno"
                            class="form-control"
                            required
                            placeholder="Enter Car Code:">

                    </div>

                    <!-- CAR NAME -->
                    <div class="form-group">

                        <input
                            type="text"
                            name="carn"
                            class="form-control"
                            required
                            placeholder="Enter Car Name:">

                    </div>

                    <!-- CAR OWNER -->
                    <div class="form-group">

                        <input
                            type="text"
                            name="caro"
                            class="form-control"
                            required
                            placeholder="Enter Car Owner Name:">

                    </div>

                    <!-- CHARGE -->
                    <div class="form-group">

                        <input
                            type="number"
                            step="0.01"
                            name="charge"
                            class="form-control"
                            required
                            placeholder="Enter The Charge:">

                    </div>

                    <!-- SUBMIT -->
                    <button
                        type="submit"
                        name="submit"
                        class="btn-register">

                        Register

                    </button>

                </form>

            </div>

            <!-- MODAL FOOTER -->
            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-dismiss="modal">

                    Close

                </button>

            </div>

        </div>
    </div>
</div>

<script>
$(document).ready(function() {

    $('#parkingCard').draggable({
        containment: "body"
    });

    $('#demo').click(function() {
        $('#test').draggable();
    });

});
</script>

</body>
</html>
