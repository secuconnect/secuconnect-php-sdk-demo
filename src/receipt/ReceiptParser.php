<?php

/**
 * Class ReceiptParser
 */
class ReceiptParser
{
    /**
     * Converts the receipt to HTML
     *
     * @param array $data
     * @return string
     */
    public static function parseReceipt(array $data)
    {
        $result = '<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8" />
    <link href="styles.css" rel="stylesheet">
    <title>Receipt</title>
<body>
    <div class="receipt">';

        if (!empty($data['header']) && is_array($data['header'])) {
            foreach ($data['header'] as $item) {
                $result .= self::parseItem($item, true);
            }
        }

        if (!empty($data['body']) && is_array($data['body'])) {
            foreach ($data['body'] as $item) {
                $result .= self::parseItem($item, false);
            }
        }

        $result .= '
        <br>
        <br>
    </div>
</body>
</html>';

        return $result;
    }


    /**
     * Converts the item to HTML
     *
     * @param array $item
     * @param bool $isHeader
     * @return string
     */
    protected static function parseItem($item, $isHeader)
    {
        if (empty($item['type'])) {
            return '';
        }

        switch ($item['type']) {
            case 'separator':
                return self::parseItemOfTypeSeparator($item, $isHeader);
            case 'textline':
                return self::parseItemOfTypeTextLine($item, $isHeader);
            case 'space':
                return self::parseItemOfTypeSpace();
            case 'name-value':
                return self::parseItemOfTypeNameValue($item, $isHeader);
        }

        return '';
    }

    /**
     * Return a list of CSS classes for the current item
     *
     * @param array $item
     * @param bool $isHeader
     * @return array
     */
    protected static function getCssClasses($item, $isHeader)
    {
        $classes = [
            'row',
        ];

        if ($isHeader) {
            $classes[] = 'receipt-header';
        }

        if (!empty($item['type'])) {
            $classes[] = $item['type'];
        }

        if (!empty($item['value']['decoration']) && is_array($item['value']['decoration'])) {
            $classes = array_merge($classes, $item['value']['decoration']);
        }

        return $classes;
    }

    /**
     * Converts the item of the type "separator" to HTML
     *
     * @param array $item
     * @param bool $isHeader
     * @return string
     */
    protected static function parseItemOfTypeSeparator($item, $isHeader)
    {
        $classes = self::getCssClasses($item, $isHeader);

        $text = isset($item['value']['caption']) ? (string)$item['value']['caption'] : '';

        if (empty($text)) {
            return '
            <div class="' . implode(' ', $classes) . '">
                <hr>
            </div>';
        }

        return '
            <div class="' . implode(' ', $classes) . '">
                <span class="col-md-12 align-center">' . $text . '</span>
            </div>';
    }

    /**
     * Converts the item of the type "textline" to HTML
     *
     * @param array $item
     * @param bool $isHeader
     * @return string
     */
    protected static function parseItemOfTypeTextLine($item, $isHeader)
    {
        $classes = self::getCssClasses($item, $isHeader);

        $text = isset($item['value']['text']) ? (string)$item['value']['text'] : '';

        return '
            <div class="' . implode(' ', $classes) . '">
                <span class="col-md-12">' . $text . '</span>
            </div>';
    }

    /**
     * Converts the item of the type "space" to HTML
     *
     * @return string
     */
    protected static function parseItemOfTypeSpace()
    {
        return '
            <br>';
    }

    /**
     * Converts the item of the type "name-value" to HTML
     *
     * @param array $item
     * @param bool $isHeader
     * @return string
     */
    protected static function parseItemOfTypeNameValue($item, $isHeader)
    {
        $classes = self::getCssClasses($item, $isHeader);

        $name = isset($item['value']['name']) ? (string)$item['value']['name'] : '';
        $value = isset($item['value']['value']) ? (string)$item['value']['value'] : '';

        return '
            <div class="' . implode(' ', $classes) . '">
                <span class="col-md-6">' . $name . '</span>
                <span class="col-md-6 align-right">' . $value . '</span>
            </div>';
    }
}
