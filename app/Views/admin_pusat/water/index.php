<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Water Management' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <div class="dashboard-header">
            <h1><i class="fas fa-tint"></i> Water Management</h1>
            <p>Manajemen data air kampus untuk UI GreenMetric</p>
        </div>

        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Coming Soon!</strong> Fitur Water Management sedang dalam pengembangan.
        </div>

        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card primary">
                    <div class="stat-icon"><i class="fas fa-tint"></i></div>
                    <div class="stat-content">
                        <h3>0</h3>
                        <p>Konsumsi Air (m³)</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card success">
                    <div class="stat-icon"><i class="fas fa-recycle"></i></div>
                    <div class="stat-content">
                        <h3>0</h3>
                        <p>Air Daur Ulang (m³)</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card warning">
                    <div class="stat-icon"><i class="fas fa-water"></i></div>
                    <div class="stat-content">
                        <h3>0</h3>
                        <p>Sumber Air</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card info">
                    <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
                    <div class="stat-content">
                        <h3>0%</h3>
                        <p>Efisiensi Air</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> Data Water Management</h3>
            </div>
            <div class="card-body">
                <div class="empty-state">
                    <i class="fas fa-tint fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Fitur ini sedang dalam pengembangan.</p>
                    <p class="text-muted">Data pengelolaan air kampus akan ditampilkan di sini.</p>
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
.stat-card { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); display: flex; align-items: center; gap: 20px; transition: all 0.3s ease; border-left: 4px solid transparent; }
.stat-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15); }
.stat-card.primary { border-left-color: #007bff; }
.stat-card.success { border-left-color: #28a745; }
.stat-card.warning { border-left-color: #ffc107; }
.stat-card.info { border-left-color: #17a2b8; }
.stat-icon { width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; color: white; }
.stat-card.primary .stat-icon { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.stat-card.success .stat-icon { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.stat-card.warning .stat-icon { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
.stat-card.info .stat-icon { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
.stat-content h3 { font-size: 28px; font-weight: 700; margin: 0 0 5px 0; color: #2c3e50; }
.stat-content p { margin: 0; color: #6c757d; font-weight: 500; font-size: 14px; }
.card { background: white; border-radius: 15px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); margin-bottom: 20px; border: none; }
.card-header { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 20px 25px; border: none; border-radius: 15px 15px 0 0; }
.card-header h3 { margin: 0; font-size: 18px; font-weight: 600; }
.card-body { padding: 25px; }
.empty-state { text-align: center; padding: 60px 20px; }
@media (max-width: 768px) { .main-content { margin-left: 0; padding: 20px; } }
</style>
