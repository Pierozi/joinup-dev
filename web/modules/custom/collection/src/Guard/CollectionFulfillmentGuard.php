<?php

namespace Drupal\collection\Guard;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\joinup_core\WorkflowUserProvider;
use Drupal\og\Og;
use Drupal\rdf_entity\RdfInterface;
use Drupal\state_machine\Guard\GuardInterface;
use Drupal\state_machine\Plugin\Workflow\WorkflowInterface;
use Drupal\state_machine\Plugin\Workflow\WorkflowTransition;

/**
 * Guard class for the transitions of the collection entity.
 *
 * @package Drupal\collection\Guard
 */
class CollectionFulfillmentGuard implements GuardInterface {

  /**
   * Virtual state.
   */
  const NON_STATE = '__new__';

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  private $entityTypeManager;

  /**
   * Holds the workflow user object needed for the checks.
   *
   * This will almost always return the logged in users but in case a check is
   * needed to be done on a different account, it should be possible.
   *
   * @var \Drupal\joinup_core\WorkflowUserProvider
   */
  private $workflowUserProvider;

  /**
   * Instantiates a CollectionFulfillmentGuard service.
   *
   * @param \Drupal\Core\Entity\EntityTypeManager $entity_type_manager
   *    The WorkflowUserProvider service.
   * @param \Drupal\joinup_core\WorkflowUserProvider $workflow_user_provider
   *    The WorkflowUserProvider service.
   */
  public function __construct(EntityTypeManager $entity_type_manager, WorkflowUserProvider $workflow_user_provider) {
    $this->entityTypeManager = $entity_type_manager;
    $this->workflowUserProvider = $workflow_user_provider;
  }

  /**
   * {@inheritdoc}
   */
  public function allowed(WorkflowTransition $transition, WorkflowInterface $workflow, EntityInterface $entity) {
    $to_state = $transition->getToState()->getId();
    // Disable virtual state.
    if ($to_state == self::NON_STATE) {
      return FALSE;
    }

    $from_state = $this->getState($entity);

    // Allowed transitions are already filtered so we only need to check
    // for the transitions defined in the settings if they include a role the
    // user has.
    // @see: collection.settings.yml
    $allowed_conditions = \Drupal::config('collection.settings')->get('transitions');

    if (\Drupal::currentUser()->hasPermission('bypass node access')) {
      return TRUE;
    }

    // Check if the user has one of the allowed system roles.
    $authorized_roles = isset($allowed_conditions[$to_state][$from_state]) ? $allowed_conditions[$to_state][$from_state] : [];
    $user = $this->workflowUserProvider->getUser();
    if (array_intersect($authorized_roles, $user->getRoles())) {
      return TRUE;
    }

    // Check if the user has one of the allowed group roles.
    $membership = Og::getMembership($entity, $user);
    return $membership && array_intersect($authorized_roles, $membership->getRolesIds());
  }

  /**
   * Retrieve the initial state value of the entity.
   *
   * @param \Drupal\rdf_entity\RdfInterface $entity
   *    The collection entity.
   *
   * @return string
   *    The machine name value of the state.
   *
   * @see https://www.drupal.org/node/2745673
   */
  protected function getState(RdfInterface $entity) {
    if ($entity->isNew()) {
      return $entity->field_ar_state->first()->value;
    }
    else {
      $unchanged_entity = $this->entityTypeManager->getStorage('rdf_entity')->loadUnchanged($entity->id());
      return $unchanged_entity->field_ar_state->first()->value;
    }
  }

}
