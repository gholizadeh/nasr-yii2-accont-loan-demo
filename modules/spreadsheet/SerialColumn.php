<?php
namespace app\modules\spreadsheet;

/**
 * SerialColumn displays a column of row numbers (1-based).
 *
 * To add a SerialColumn to the [[Spreadsheet]], add it to the [[Spreadsheet::$columns|columns]] configuration as follows:
 *
 * ```php
 * 'columns' => [
 *     [
 *         'class' => \app\modules\spreadsheet\SerialColumn::class,
 *     ],
 *     // ...
 * ]
 * ```
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class SerialColumn extends Column
{
    /**
     * {@inheritdoc}
     */
    public $header = '#';


    /**
     * {@inheritdoc}
     */
    public function renderDataCellContent($model, $key, $index)
    {
        return $index + 1;
    }
}