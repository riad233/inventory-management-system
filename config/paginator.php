<?php
/**
 * Paginator Helper - Bootstrap 5 pagination
 * Handles pagination logic for list views
 */

class Paginator {
    
    /**
     * Get pagination data for a result set
     * 
     * @param int $totalItems Total number of items
     * @param int $page Current page number
     * @param int $perPage Items per page
     * @return array Pagination data with LIMIT/OFFSET
     */
    public static function paginate($totalItems, $page = 1, $perPage = 50) {
        $page = max(1, $page);
        $totalPages = ceil($totalItems / $perPage);
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;
        
        return [
            'page' => $page,
            'perPage' => $perPage,
            'offset' => $offset,
            'totalItems' => $totalItems,
            'totalPages' => $totalPages,
            'hasPrevious' => $page > 1,
            'hasNext' => $page < $totalPages,
            'previousPage' => $page - 1,
            'nextPage' => $page + 1
        ];
    }
    
    /**
     * Generate SQL LIMIT clause
     */
    public static function getLimit($page = 1, $perPage = 50) {
        $data = self::paginate(PHP_INT_MAX, $page, $perPage);
        return "LIMIT " . $data['perPage'] . " OFFSET " . $data['offset'];
    }
    
    /**
     * Generate pagination HTML (Bootstrap 5)
     */
    public static function renderHtml($pagination, $baseUrl) {
        if ($pagination['totalPages'] <= 1) {
            return '';
        }
        
        $html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';
        
        // Previous button
        if ($pagination['hasPrevious']) {
            $html .= '<li class="page-item">
                <a class="page-link" href="' . e($baseUrl) . '&page=' . $pagination['previousPage'] . '">Previous</a>
            </li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">Previous</span></li>';
        }
        
        // Page numbers
        $startPage = max(1, $pagination['page'] - 2);
        $endPage = min($pagination['totalPages'], $pagination['page'] + 2);
        
        if ($startPage > 1) {
            $html .= '<li class="page-item"><a class="page-link" href="' . e($baseUrl) . '&page=1">1</a></li>';
            if ($startPage > 2) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }
        
        for ($i = $startPage; $i <= $endPage; $i++) {
            if ($i == $pagination['page']) {
                $html .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
            } else {
                $html .= '<li class="page-item"><a class="page-link" href="' . e($baseUrl) . '&page=' . $i . '">' . $i . '</a></li>';
            }
        }
        
        if ($endPage < $pagination['totalPages']) {
            if ($endPage < $pagination['totalPages'] - 1) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            $html .= '<li class="page-item"><a class="page-link" href="' . e($baseUrl) . '&page=' . $pagination['totalPages'] . '">' . $pagination['totalPages'] . '</a></li>';
        }
        
        // Next button
        if ($pagination['hasNext']) {
            $html .= '<li class="page-item">
                <a class="page-link" href="' . e($baseUrl) . '&page=' . $pagination['nextPage'] . '">Next</a>
            </li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">Next</span></li>';
        }
        
        $html .= '</ul></nav>';
        return $html;
    }
    
    /**
     * Get page number from GET parameter
     */
    public static function getPage() {
        $page = $_GET['page'] ?? 1;
        return max(1, (int)$page);
    }
}
?>
