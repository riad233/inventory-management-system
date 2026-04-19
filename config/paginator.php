<?php
/**
 * Pagination Helper for IMS
 * Handles pagination of large datasets
 */

class Paginator {
    private $totalItems;
    private $itemsPerPage;
    private $currentPage;
    private $totalPages;
    
    public function __construct($totalItems, $itemsPerPage = 10, $currentPage = 1) {
        $this->totalItems = (int)$totalItems;
        $this->itemsPerPage = (int)$itemsPerPage;
        $this->currentPage = max(1, (int)$currentPage);
        $this->totalPages = ceil($this->totalItems / $this->itemsPerPage);
        
        // Ensure current page doesn't exceed total pages
        if ($this->totalPages > 0 && $this->currentPage > $this->totalPages) {
            $this->currentPage = $this->totalPages;
        }
    }
    
    /**
     * Get the offset for database queries
     */
    public function getOffset() {
        return ($this->currentPage - 1) * $this->itemsPerPage;
    }
    
    /**
     * Get items per page
     */
    public function getLimit() {
        return $this->itemsPerPage;
    }
    
    /**
     * Get current page number
     */
    public function getCurrentPage() {
        return $this->currentPage;
    }
    
    /**
     * Get total number of pages
     */
    public function getTotalPages() {
        return $this->totalPages;
    }
    
    /**
     * Get total number of items
     */
    public function getTotalItems() {
        return $this->totalItems;
    }
    
    /**
     * Check if there's a previous page
     */
    public function hasPreviousPage() {
        return $this->currentPage > 1;
    }
    
    /**
     * Check if there's a next page
     */
    public function hasNextPage() {
        return $this->currentPage < $this->totalPages;
    }
    
    /**
     * Get previous page number
     */
    public function getPreviousPage() {
        return max(1, $this->currentPage - 1);
    }
    
    /**
     * Get next page number
     */
    public function getNextPage() {
        return min($this->totalPages, $this->currentPage + 1);
    }
    
    /**
     * Generate pagination HTML (Bootstrap 5 compatible)
     */
    public function render($url = '') {
        if ($this->totalPages <= 1) {
            return '';
        }
        
        $html = '<nav aria-label="Page navigation"><ul class="pagination">';
        
        // Previous button
        if ($this->hasPreviousPage()) {
            $html .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($url) . '&page=' . $this->getPreviousPage() . '">Previous</a></li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">Previous</span></li>';
        }
        
        // Page numbers
        $start = max(1, $this->currentPage - 2);
        $end = min($this->totalPages, $this->currentPage + 2);
        
        if ($start > 1) {
            $html .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($url) . '&page=1">1</a></li>';
            if ($start > 2) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }
        
        for ($i = $start; $i <= $end; $i++) {
            if ($i == $this->currentPage) {
                $html .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
            } else {
                $html .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($url) . '&page=' . $i . '">' . $i . '</a></li>';
            }
        }
        
        if ($end < $this->totalPages) {
            if ($end < $this->totalPages - 1) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            $html .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($url) . '&page=' . $this->totalPages . '">' . $this->totalPages . '</a></li>';
        }
        
        // Next button
        if ($this->hasNextPage()) {
            $html .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($url) . '&page=' . $this->getNextPage() . '">Next</a></li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">Next</span></li>';
        }
        
        $html .= '</ul></nav>';
        
        return $html;
    }
    
    /**
     * Get info string like "Showing 1-10 of 100"
     */
    public function getInfo() {
        $start = ($this->currentPage - 1) * $this->itemsPerPage + 1;
        $end = min($this->currentPage * $this->itemsPerPage, $this->totalItems);
        
        return "Showing $start-$end of $this->totalItems";
    }
}
?>
