<?php

require '../config/config.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
    header('Location: ../index.php');
    exit;
}

$db = new Database();
$con = $db->conectar();

$fechaIni = isset($_POST['fecha_ini']) ? $_POST['fecha_ini'] : '';
$fechaFin = isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : '';

$visitasPorDia = [];
$ventasPorTemporada = [];

if ($fechaIni && $fechaFin) {
    $visitasPorDia = obtenerVisitas($con, $fechaIni, $fechaFin);
    $ventasPorTemporada = obtenerVentasEstacionales($con, $fechaIni, $fechaFin);
}

function existeTabla($con, $tabla)
{
    $stmt = $con->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?");
    $stmt->execute([$tabla]);
    return $stmt->fetchColumn() > 0;
}

function obtenerVisitas($con, $fechaIni, $fechaFin)
{
    if (!existeTabla($con, 'visitas')) {
        return [];
    }

    $sql = "SELECT DATE(fecha) AS dia, COUNT(*) AS total FROM visitas WHERE DATE(fecha) BETWEEN ? AND ? GROUP BY dia ORDER BY dia";
    $stmt = $con->prepare($sql);
    $stmt->execute([$fechaIni, $fechaFin]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerVentasEstacionales($con, $fechaIni, $fechaFin)
{
    $sql = "SELECT CASE
            WHEN MONTH(fecha) IN (12, 1, 2) THEN 'Invierno'
            WHEN MONTH(fecha) IN (3, 4, 5) THEN 'Primavera'
            WHEN MONTH(fecha) IN (6, 7, 8) THEN 'Verano'
            WHEN MONTH(fecha) IN (9, 10, 11) THEN 'Otoño'
        END AS temporada,
        SUM(total) AS total
        FROM compra
        WHERE DATE(fecha) BETWEEN ? AND ?
            AND (status LIKE 'COMPLETED' OR status LIKE 'approved')
        GROUP BY temporada
        ORDER BY FIELD(temporada, 'Invierno', 'Primavera', 'Verano', 'Otoño')";

    $stmt = $con->prepare($sql);
    $stmt->execute([$fechaIni, $fechaFin]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

require '../header.php';

?>
<!-- Contenido -->
<main class="flex-shrink-0">
    <div class="container mt-3">
        <h4>Estadísticas de visitas y ventas estacionales</h4>

        <form action="estadisticas.php" method="post" autocomplete="off" class="mb-4">
            <div class="row mb-2">
                <div class="col-12 col-md-4">
                    <label for="fecha_ini" class="form-label">Fecha inicial:</label>
                    <input type="date" class="form-control" name="fecha_ini" id="fecha_ini" value="<?php echo htmlspecialchars($fechaIni); ?>" required>
                </div>
                <div class="col-12 col-md-4">
                    <label for="fecha_fin" class="form-label">Fecha final:</label>
                    <input type="date" class="form-control" name="fecha_fin" id="fecha_fin" value="<?php echo htmlspecialchars($fechaFin); ?>" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Consultar</button>
        </form>

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-eye me-2"></i>Visitas por día</span>
                        <small class="text-muted">Según rango seleccionado</small>
                    </div>
                    <div class="card-body">
                        <?php if ($fechaIni && $fechaFin && !empty($visitasPorDia)) { ?>
                            <canvas id="chart-visitas" height="120"></canvas>
                        <?php } elseif ($fechaIni && $fechaFin) { ?>
                            <div class="alert alert-warning mb-0">No se encontraron visitas registradas en el rango seleccionado.</div>
                        <?php } else { ?>
                            <p class="text-muted mb-0">Selecciona un rango de fechas para ver el comportamiento de visitas.</p>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-leaf me-2"></i>Ventas por temporada</span>
                        <small class="text-muted">Agrupadas por estación</small>
                    </div>
                    <div class="card-body">
                        <?php if ($fechaIni && $fechaFin && !empty($ventasPorTemporada)) { ?>
                            <canvas id="chart-estaciones" height="120"></canvas>
                        <?php } elseif ($fechaIni && $fechaFin) { ?>
                            <div class="alert alert-warning mb-0">No se registraron ventas en el rango seleccionado.</div>
                        <?php } else { ?>
                            <p class="text-muted mb-0">Consulta para visualizar la distribución estacional de las ventas.</p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php if ($fechaIni && $fechaFin && !empty($visitasPorDia)) { ?>
    <script>
        const visitasData = <?php echo json_encode($visitasPorDia); ?>;
        const visitasLabels = visitasData.map(item => item.dia);
        const visitasTotals = visitasData.map(item => item.total);

        const ctxVisitas = document.getElementById('chart-visitas');
        new Chart(ctxVisitas, {
            type: 'line',
            data: {
                labels: visitasLabels,
                datasets: [{
                    label: 'Visitas',
                    data: visitasTotals,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.15)',
                    tension: 0.25,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
<?php } ?>

<?php if ($fechaIni && $fechaFin && !empty($ventasPorTemporada)) { ?>
    <script>
        const estacionesData = <?php echo json_encode($ventasPorTemporada); ?>;
        const estacionesLabels = estacionesData.map(item => item.temporada);
        const estacionesTotals = estacionesData.map(item => item.total);

        const ctxEstaciones = document.getElementById('chart-estaciones');
        new Chart(ctxEstaciones, {
            type: 'bar',
            data: {
                labels: estacionesLabels,
                datasets: [{
                    label: 'Ventas',
                    data: estacionesTotals,
                    backgroundColor: ['#6f42c1', '#17a2b8', '#ffc107', '#28a745'],
                    borderColor: ['#6f42c1', '#17a2b8', '#ffc107', '#28a745'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
<?php } ?>

<?php include '../footer.php'; ?>