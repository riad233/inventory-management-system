<div class="error-page-container">
    <div class="error-card">

        <div class="error-icon">
            <i class="fas fa-lock"></i>
        </div>

        <h1 class="error-code">403</h1>
        <h2 class="error-title">Access Denied</h2>

        <p class="error-message">
            You don't have permission to access this page.<br>
            Please contact an administrator if you believe this is a mistake.
        </p>

        <div class="error-role-info">
            Your current role:&nbsp;
            <span class="role-pill role-pill-<?php echo strtolower(e($_SESSION['role'] ?? 'unknown')); ?>">
                <?php echo e($_SESSION['role'] ?? 'Unknown'); ?>
            </span>
        </div>

        <div class="error-actions">
            <a href="?url=dashboard/index" class="btn btn-primary">
                <i class="fas fa-home"></i> Dashboard
            </a>
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
    color: #dc3545;
    margin-bottom: 12px;
}
.error-code {
    font-size: 5.5rem;
    font-weight: 800;
    color: #dc3545;
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
    margin-bottom: 24px;
}
.error-role-info {
    display: inline-flex;
    align-items: center;
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 8px 16px;
    font-size: 0.9rem;
    color: #555;
    margin-bottom: 32px;
}
.role-pill {
    display: inline-block;
    border-radius: 20px;
    padding: 2px 14px;
    font-weight: 600;
    font-size: 0.88rem;
    margin-left: 4px;
}
.role-pill-admin    { background: #fdecea; color: #c0392b; border: 1px solid #f5c6cb; }
.role-pill-manager  { background: #fff3cd; color: #856404; border: 1px solid #ffc107; }
.role-pill-employee { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
.role-pill-unknown  { background: #f0f0f0; color: #555;    border: 1px solid #ccc; }
.error-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
    flex-wrap: wrap;
}
</style>
