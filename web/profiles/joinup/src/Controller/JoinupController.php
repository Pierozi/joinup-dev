<?php

namespace Drupal\joinup\Controller;

use Drupal\Component\Utility\Unicode;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\rdf_entity\RdfEntityTypeInterface;

/**
 * Provides route responses for Joinup.
 */
class JoinupController extends ControllerBase {

  /**
   * Provides propose forms for various RDF entities.
   *
   * This is used for the propose form of collections and solutions.
   *
   * @param \Drupal\rdf_entity\RdfEntityTypeInterface $rdf_type
   *   The RDF bundle entity for which to generate the propose form.
   *
   * @return array
   *   A render array for the propose form.
   *
   * @throws \Exception
   *   Thrown when an attempt is made to propose an rdf_entity that is not a
   *   solution or a collection.
   */
  public function proposeRdfEntity(RdfEntityTypeInterface $rdf_type) {
    $rdf_entity = $this->entityTypeManager()->getStorage('rdf_entity')->create(array(
      'rid' => $rdf_type->id(),
    ));

    $form = $this->entityFormBuilder()->getForm($rdf_entity, 'propose');
    $form['#title'] = $this->t('Propose @type', [
      '@type' => Unicode::strtolower($rdf_type->label()),
    ]);
    return $form;
  }

  /**
   * Handles access to the rdf_entity proposal form.
   *
   * @param \Drupal\rdf_entity\RdfEntityTypeInterface $rdf_type
   *   The RDF entity type for which the proposal form is built.
   *
   * @return \Drupal\Core\Access\AccessResult
   *   The access result object.
   */
  public function createAssetReleaseAccess(RdfEntityTypeInterface $rdf_type) {
    if (!in_array($rdf_type->id(), ['collection', 'solution'])) {
      return AccessResult::forbidden();
    }
    return AccessResult::allowedIf($this->currentUser()->hasPermission("propose {$rdf_type->id()} rdf entity"));
  }

  /**
   * Provides empty homepage..
   *
   * @return array
   *   A render array for the homepage.
   */
  public function homepageContent() {
    $build = array();
    return $build;
  }

}
