<div class="error-page-container">
    <div class="error-card">

        <div class="error-icon error-icon-danger">
            <i class="fas fa-exclamation-triangle"></i>
        </div>

        <h1 class="error-code">500</h1>
        <h2 class="error-title">Server Error</h2>

        <p class="error-message">
            <?php echo htmlspecialchars($data['message'] ?? 'An internal server error occurred'); ?><br>
            Our team has been notified of this issue. Please try again later.
        </p>

        <div class="error-details">
            <?php if (defined('APP_DEBUG') && APP_DEBUG): ?>
            <div class="alert alert-warning mt-3" role="alert">
                <strong>Debug Information:</strong>
                <pre style="margin-top: 10px; background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto;">
<?php 
$exception = $GLOBALS['exception'] ?? null;
echo htmlspecialchars(
    var_export([
        'exception' => $exception ? get_class($exception) : 'Unknown',
        'message' => $exception ? $exception->getMessage() : 'Unknown',
        'file' => $exception ? $exception->getFile() : 'Unknown',
        'line' => $exception ? $exception->getLine() : 0,
    ], true)
); 
?>
                </pre>
            </div>
            <?php endif; ?>
        </div>

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
