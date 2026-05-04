<div class="error-page-container">
    <div class="error-card">

        <div class="error-icon">
            <i class="fas fa-search"></i>
        </div>

        <h1 class="error-code">404</h1>
        <h2 class="error-title">Page Not Found</h2>

        <p class="error-message">
            The page you are looking for does not exist or may have been moved.<br>
            Please check the URL or return to the dashboard.
        </p>

        <div class="error-actions">
            <?php if (!empty($_SESSION['username'])): ?>
            <a href="?url=dashboard/index" class="btn btn-primary">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <?php else: ?>
            <a href="?url=home/index" class="btn btn-primary">
                <i class="fas fa-home"></i> Home
            </a>
            <?php endif; ?>
            <a href="javascript:history.back()" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Go Back
            </a>
        </div>

    </div>
</div>

<style>
.error-page-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: calc(100vh - 160px);
    padding: 40px 20px;
}
.error-card {
    text-align: center;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.10);
    padding: 60px 48px;
    max-width: 480px;
    width: 100%;
    animation: fadeInUp 0.35s ease;
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(24px); }
    to   { opacity: 1; transform: translateY(0); }
}
.error-icon {
    font-size: 4rem;
    color: #6c757d;
    margin-bottom: 12px;
}
.error-code {
    font-size: 5.5rem;
    font-weight: 800;
    color: #6c757d;
    margin: 0 0 8px;
    line-height: 1;
    letter-spacing: -2px;
}
.error-title {
    font-size: 1.6rem;
    font-weight: 600;
    color: #2d2d2d;
    margin-bottom: 14px;
}
.error-message {
    color: #666;
    font-size: 0.97rem;
    line-height: 1.6;
    margin-bottom: 32px;
}
.error-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
    flex-wrap: wrap;
}
</style>
