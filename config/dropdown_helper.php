<?php

/**
 * Dropdown Helper Functions
 * Provides utility functions to load and render dropdown options
 */

class DropdownHelper
{
    private static $dropdowns = null;

    /**
     * Load dropdown data from JSON file
     */
    public static function load()
    {
        if (self::$dropdowns === null) {
            $json_path = __DIR__ . '/dropdowns.json';
            if (file_exists($json_path)) {
                $json_content = file_get_contents($json_path);
                self::$dropdowns = json_decode($json_content, true);
            } else {
                self::$dropdowns = [];
            }
        }
        return self::$dropdowns;
    }

    /**
     * Get a specific dropdown array
     * @param string $key The dropdown key (e.g., 'departments', 'asset_categories')
     * @return array
     */
    public static function get($key)
    {
        $data = self::load();
        return $data[$key] ?? [];
    }

    /**
     * Render HTML option elements for a select field
     * @param string $key The dropdown key
     * @param string|null $selected The currently selected value (id)
     * @return string HTML option tags
     */
    public static function renderOptions($key, $selected = null)
    {
        $options = self::get($key);
        $html = '';
        
        foreach ($options as $option) {
            $is_selected = ($selected !== null && $option['id'] == $selected) ? 'selected' : '';
            $html .= '<option value="' . htmlspecialchars($option['id']) . '" ' . $is_selected . '>';
            $html .= htmlspecialchars($option['name']);
            $html .= '</option>' . PHP_EOL;
        }
        
        return $html;
    }

    /**
     * Get dropdown name by ID
     * @param string $key The dropdown key
     * @param int $id The option ID
     * @return string|null
     */
    public static function getName($key, $id)
    {
        $options = self::get($key);
        
        foreach ($options as $option) {
            if ($option['id'] == $id) {
                return $option['name'];
            }
        }
        
        return null;
    }
}
?>
