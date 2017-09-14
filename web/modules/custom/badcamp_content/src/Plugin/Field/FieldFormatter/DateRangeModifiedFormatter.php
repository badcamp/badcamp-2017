<?php

namespace Drupal\badcamp_content\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\datetime_range\DateTimeRangeTrait;
use Drupal\datetime_range\Plugin\Field\FieldFormatter\DateRangeDefaultFormatter;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;

/**
 * Plugin implementation of the 'Modified' formatter for 'daterange' fields.
 *
 * This formatter renders the data range using <time> elements, with
 * configurable date formats (from the list of configured formats) and a
 * separator.
 *
 * @FieldFormatter(
 *   id = "daterange_modified",
 *   label = @Translation("Modified"),
 *   field_types = {
 *     "daterange"
 *   }
 * )
 */
class DateRangeModifiedFormatter extends DateRangeDefaultFormatter {

  use DateTimeRangeTrait;

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        'same_date_date_format' => 'medium',
        'same_date_time_format' => 'medium',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    $separator = $this->getSetting('separator');

    foreach ($items as $delta => $item) {
      if (!empty($item->start_date) && !empty($item->end_date)) {
        /** @var \Drupal\Core\Datetime\DrupalDateTime $start_date */
        $start_date = $item->start_date;
        /** @var \Drupal\Core\Datetime\DrupalDateTime $end_date */
        $end_date = $item->end_date;

        if ($start_date->format('Y-m-d') === $end_date->format('Y-m-d')) {
          $elements[$delta] = [
            'day' => $this->buildModifiedDateWithIsoAttribute($start_date, 'same_date_date_format'),
            'day_time_separator' => ['#plain_text' => ' '],
            'start_date' => $this->buildModifiedDateWithIsoAttribute($start_date),
            'separator' => ['#plain_text' => ' ' . $separator . ' '],
            'end_date' => $this->buildModifiedDateWithIsoAttribute($end_date),
          ];
        }
        else {
          if ($start_date->getTimestamp() !== $end_date->getTimestamp()) {
            $elements[$delta] = [
              'start_date' => $this->buildDateWithIsoAttribute($start_date),
              'separator' => ['#plain_text' => ' ' . $separator . ' '],
              'end_date' => $this->buildDateWithIsoAttribute($end_date),
            ];
          }
          else {
            $elements[$delta] = $this->buildDateWithIsoAttribute($start_date);
            if (!empty($item->_attributes)) {
              $elements[$delta]['#attributes'] += $item->_attributes;
              // Unset field item attributes since they have been included in the
              // formatter output and should not be rendered in the field template.
              unset($item->_attributes);
            }
          }
        }
      }
    }

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    $time = new DrupalDateTime();
    $format_types = $this->dateFormatStorage->loadMultiple();
    $options = [];
    foreach ($format_types as $type => $type_info) {
      $format = $this->dateFormatter->format($time->getTimestamp(), $type);
      $options[$type] = $type_info->label() . ' (' . $format . ')';
    }

    $form['same_date_date_format'] = [
      '#type' => 'select',
      '#title' => t('Same Date Day format'),
      '#description' => t("Choose a format for displaying the date. Be sure to set a format appropriate for the field, i.e. omitting time for a field that only has a date."),
      '#options' => $options,
      '#default_value' => $this->getSetting('same_date_date_format'),
    ];

    $form['same_date_time_format'] = [
      '#type' => 'select',
      '#title' => t('Same Date Time format'),
      '#description' => t("Choose a format for displaying the date. Be sure to set a format appropriate for the field, i.e. omitting time for a field that only has a date."),
      '#options' => $options,
      '#default_value' => $this->getSetting('same_date_time_format'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();

    $date = new DrupalDateTime();
    $summary[] = t('Same Date Day Format: @display', ['@display' => $this->formatSpecificDate($date, 'same_date_date_format')]);
    $summary[] = t('Same Date Time Format: @display', ['@display' => $this->formatSpecificDate($date, 'same_date_time_format')]);

    return $summary;
  }

  /**
   * Creates a render array from a date object with ISO date attribute.
   *
   * @param \Drupal\Core\Datetime\DrupalDateTime $date
   *   A date object.
   *
   * @return array
   *   A render array.
   */
  protected function buildModifiedDateWithIsoAttribute(DrupalDateTime $date, $format = 'same_date_time_format') {
    if ($this->getFieldSetting('datetime_type') == DateTimeItem::DATETIME_TYPE_DATE) {
      // A date without time will pick up the current time, use the default.
      datetime_date_default_time($date);
    }

    // Create the ISO date in Universal Time.
    $iso_date = $date->format("Y-m-d\TH:i:s") . 'Z';

    $this->setTimeZone($date);

    $build = [
      '#theme' => 'time',
      '#text' => $this->formatSpecificDate($date, $format),
      '#html' => FALSE,
      '#attributes' => [
        'datetime' => $iso_date,
      ],
      '#cache' => [
        'contexts' => [
          'timezone',
        ],
      ],
    ];

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  protected function formatSpecificDate($date, $format) {
    $format_type = $this->getSetting($format);
    $timezone = $this->getSetting('timezone_override') ?: $date->getTimezone()->getName();
    return $this->dateFormatter->format($date->getTimestamp(), $format_type, '', $timezone != '' ? $timezone : NULL);
  }

}
