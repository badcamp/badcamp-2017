<?php

namespace Drupal\badcamp_content\Plugin\Action;

use Drupal\Core\Link;
use Drupal\node\Entity\Node;
use Drupal\views_bulk_operations\Action\ViewsBulkOperationsActionBase;
use Drupal\views_bulk_operations\Action\ViewsBulkOperationsPreconfigurationInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * An action to unapprove sessions
 *
 * @Action(
 *   id = "views_bulk_unapprove_sessions",
 *   label = @Translation("Unapprove Sessions"),
 *   type = "node"
 * )
 */
class ViewsBulkUnApproveSessions extends ViewsBulkOperationsActionBase implements ViewsBulkOperationsPreconfigurationInterface {

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    if ($entity instanceof \Drupal\node\NodeInterface && $entity->bundle() == 'session') {
      $entity->set('field_session_status', 'Proposed');
      $entity->save();
    }
    return sprintf('Session set to Proposed: %s', $entity->getTitle());
  }

  /**
   * {@inheritdoc}
   */
  public function buildPreConfigurationForm(array $form, array $values, FormStateInterface $form_state) {
    return $form;
  }

  /**
   * Configuration form builder.
   *
   * If this method has implementation, the action is
   * considered to be configurable.
   *
   * @param array $form
   *   Form array.
   * @param Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   *
   * @return array
   *   The configuration form.
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['#title'] = $this->t('Set the selected sessions to Proposed');

    $nids = array_reduce($form_state->getStorage()['list'], function($carry, $item){
      $carry[] = $item[3];
      return $carry;
    });

    $nodes = Node::loadMultiple($nids);
    $titles = array_reduce($nodes, function($carry, $item){
      $carry[] = Link::createFromRoute($item->getTitle(),
        'entity.node.canonical',
        ['node' => $item->id()]
      );
      return $carry;
    });

    $form['list'] = array(
      '#theme' => 'item_list',
      '#list_type' => 'ul',
      '#items' => $titles,
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    if ($object->getEntityType() === 'node') {
      $access = $object->access('update', $account, TRUE)
        ->andIf($object->status->access('edit', $account, TRUE));
      return $return_as_object ? $access : $access->isAllowed();
    }

    return TRUE;
  }

}
