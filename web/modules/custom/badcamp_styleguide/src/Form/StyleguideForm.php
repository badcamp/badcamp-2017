<?php

namespace Drupal\badcamp_styleguide\Form;

use Drupal\Component\Utility\SafeMarkup;
use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;

class StyleguideForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'badcamp_style_guide';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // This form should never actually be submitted.
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Build a list of styleguide sections, inside a
    $form['tabs'] = [
      '#type' => 'vertical_tabs',
      '#default_tab' => 'basic',
    ];
    foreach(self::sections() as $id => $component) {
      $form[$id] = [
        '#type' => 'details',
        '#title' => $id,
        '#group' => 'tabs',
        'content' => $component,
      ];
    }
    return $form;
  }

  /**
   * Get a renderable array of sections in the styleguide.
   *
   * @return array
   */
  public function sections() {
    return [
      'Headings' => $this->templatedSection('headings'),
      'Paragraphs' => $this->templatedSection('paragraphs'),
      'Lists' => $this->templatedSection('lists'),
      'Tables' => $this->templatedSection('table'),
      'Buttons' => $this->templatedSection('buttons'),
      'Forms' => $this->formsSection(),
      'Breadcrumb' => $this->breadcrumbSection(),
    ];
  }

  /**
   * Build a simple section by pulling in an HTML file.
   *
   * HTML files should be stored in the html folder.
   *
   * @param $name
   *    The name of the HTML file, before the . (forms for forms.html)
   *
   * @return array
   */
  private function templatedSection($name) {
    $contents = file_get_contents(sprintf('%s/html/%s.html', __DIR__.'/../../', $name));
    return [
      '#markup' => SafeMarkup::format($contents, [])
    ];
  }

  /**
   * This is an example of building a more complex section.
   *
   * Build up a breadcrumb and pass it through the theme system.
   */
  public function breadcrumbSection() {
    $breadcrumb = new Breadcrumb();
    $front_url = Url::fromUserInput('/');
    $breadcrumb->setLinks([
      new Link('Home', $front_url),
      new Link('Some', $front_url),
      new Link('Nested', $front_url),
      new Link('Path', $front_url),
    ]);
    return $breadcrumb->toRenderable();
  }

  public function formsSection() {
    $build['text'] = [
      '#type' => 'textfield',
      '#title' => t('Textfield'),
      '#placeholder' => t('Placeholder'),
    ];
    $build['email'] = [
      '#type' => 'email',
      '#title' => t('E-Mail'),
      '#placeholder' => t('Placeholder'),
    ];
    $build['textarea'] = [
      '#type' => 'textarea',
      '#title' => t('Textarea'),
      '#placeholder' => t('Placeholder'),
    ];
    $build['select'] = [
      '#title' => t('Select'),
      '#type' => 'select',
      '#options' => ['option 1', 'option 2'],
    ];
    $build['checkboxes'] = [
      '#title' => t('Checkboxes'),
      '#type' => 'checkboxes',
      '#options' => ['option 1', 'option 2'],
    ];
    $build['radios'] = [
      '#title' => t('Radios'),
      '#type' => 'radios',
      '#options' => ['option 1', 'option 2'],
    ];
    $build['file'] = [
      '#type' => 'file',
      '#title' => t('File'),
    ];
    return $build;
  }
}