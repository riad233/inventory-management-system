/**
 * Asset Minifier Utility
 * Minifies CSS and JavaScript files for production
 * 
 * Usage: Call from CLI or include in build process
 */

class AssetMinifier {
    
    /**
     * Minify CSS content
     */
    public static function minifyCSS($css) {
        // Remove comments
        $css = preg_replace('!/\*[^*]*\*+(?:[^/*][^*]*\*+)*/!', '', $css);
        
        // Remove whitespace
        $css = preg_replace('/\s+/', ' ', $css);
        $css = preg_replace('/\s*([{};:,>+~])\s*/', '$1', $css);
        
        // Remove trailing semicolons
        $css = str_replace(';}', '}', $css);
        
        return trim($css);
    }
    
    /**
     * Minify JavaScript content
     */
    public static function minifyJS($js) {
        // Remove single-line comments
        $js = preg_replace('#//.*$#m', '', $js);
        
        // Remove multi-line comments
        $js = preg_replace('#/\*.*?\*/#s', '', $js);
        
        // Remove whitespace
        $js = preg_replace('/\s+/', ' ', $js);
        $js = preg_replace('/\s*([{};:,=()[\]>+<&|!])\s*/', '$1', $js);
        
        return trim($js);
    }
    
    /**
     * Minify CSS file and save
     */
    public static function minifyCSSFile($inputPath, $outputPath) {
        if (!file_exists($inputPath)) {
            return false;
        }
        
        $css = file_get_contents($inputPath);
        $minified = self::minifyCSS($css);
        
        file_put_contents($outputPath, $minified);
        return true;
    }
    
    /**
     * Minify JavaScript file and save
     */
    public static function minifyJSFile($inputPath, $outputPath) {
        if (!file_exists($inputPath)) {
            return false;
        }
        
        $js = file_get_contents($inputPath);
        $minified = self::minifyJS($js);
        
        file_put_contents($outputPath, $minified);
        return true;
    }
    
    /**
     * Get size reduction info
     */
    public static function getSizeReduction($originalSize, $minifiedSize) {
        $reduction = $originalSize - $minifiedSize;
        $percentage = ($reduction / $originalSize) * 100;
        
        return [
            'original' => $originalSize,
            'minified' => $minifiedSize,
            'saved' => $reduction,
            'percentage' => round($percentage, 2)
        ];
    }
}
?>
