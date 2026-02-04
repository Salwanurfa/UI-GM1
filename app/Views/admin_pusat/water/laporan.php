<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Laporan Water Management' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <div class="dashboard-header">
            <h1><i class="fas fa-chart-bar"></i> Laporan Water Management</h1>
            <p>Laporan dan analisis data pengelolaan air kampus</p>
        </div>

        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Coming Soon!</strong> Fitur laporan water management sedang dalam pengembangan.
        </div>

        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-file-alt"></i> Laporan Water Management</h3>
            </div>
            <div class="card-body">
                <div class="empty-state">
                    <i class="fas fa-chart-area fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Laporan water management akan ditampilkan di sini.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<style>
body { margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8f9fa; }
.main-content { margin-left: 280px; padding: 30px; min-height: 100vh; }
.dashboard-header { margin-bottom: 30px; padding: 20px 0; border-bottom: 2px solid #e9ecef; }
.dashboard-header h1 { color: #2c3e50; margin-bottom: 10px; font-size: 28px; font-weight: 700; }
.dashboard-header p { color: #6c757d; font-size: 16px; margin: 0; }
.card { background: white; border-radius: 15px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); margin-bottom: 20px; border: none; }
.card-header { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 20px 25px; border: none; border-radius: 15px 15px 0 0; }
.card-header h3 { margin: 0; font-size: 18px; font-weight: 600; }
.card-body { padding: 25px; }
.empty-state { text-align: center; padding: 60px 20px; }
@media (max-width: 768px) { .main-content { margin-left: 0; padding: 20px; } }
</style>
